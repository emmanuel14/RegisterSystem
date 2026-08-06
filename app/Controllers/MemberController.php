<?php

namespace Controllers;

use Helpers\Helper;
use Helpers\Session;
use Helpers\QRCode;
use Models\Registration;
use Models\Notification;

class MemberController extends BaseController
{
    public function loginPage(): void
    {
        if (Session::get('member_id')) {
            Helper::redirect('/member/profile');
        }
        $this->view('member/login', [], 'public');
    }

    public function loginSubmit(): void
    {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $code  = Helper::sanitizeString($_POST['registration_code'] ?? '');

        if (!$email || !$code) {
            Session::flash('error', 'Please enter your email and a registration code.');
            Helper::redirect('/member/login');
        }

        $reg = Registration::findByCode($code);
        if (!$reg || strtolower($reg['email']) !== strtolower($email)) {
            Session::flash('error', 'Invalid email or registration code.');
            Helper::redirect('/member/login');
        }

        Session::set('member_id', $reg['attendee_id']);
        Session::set('member_name', $reg['first_name'] . ' ' . $reg['last_name']);
        Helper::redirect('/member/profile');
    }

    public function logout(): void
    {
        Session::delete('member_id');
        Session::delete('member_name');
        Helper::redirect('/');
    }

    public function profile(): void
    {
        $attendeeId = Session::get('member_id');
        if (!$attendeeId) {
            Helper::redirect('/member/login');
        }

        $attendee      = Registration::getAttendeeProfile((int)$attendeeId);
        $registrations = Registration::byAttendee((int)$attendeeId);
        $upcoming      = Registration::upcomingByAttendee((int)$attendeeId);
        $notifications = Notification::forAttendee((int)$attendeeId);

        $this->view('member/profile', compact('attendee', 'registrations', 'upcoming', 'notifications'), 'public');
    }

    public function pass(string $code): void
    {
        $reg = Registration::findByCode($code);
        if (!$reg) {
            http_response_code(404);
            $this->view('public/404', [], 'public');
            return;
        }

        // Verify ownership if logged in
        $memberId = Session::get('member_id');
        if ($memberId && (int)$memberId !== (int)$reg['attendee_id']) {
            Session::flash('error', 'You do not have access to this pass.');
            Helper::redirect('/member/profile');
        }

        $qrFile = QRCode::filename($code);
        $qrUrl  = file_exists(QR_STORAGE_PATH . '/' . $qrFile)
            ? Helper::base('uploads/qrcodes/' . $qrFile)
            : null;

        $attendee = Registration::getAttendeeProfile((int)$reg['attendee_id']);
        $this->view('member/pass', compact('reg', 'qrUrl', 'code', 'attendee'), 'public');
    }

    public function submitTestimonial(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('/');
        }

        $attendeeId = Session::get('member_id');
        if (!$attendeeId) {
            Helper::redirect('/member/login');
        }

        $content = Helper::sanitizeString($_POST['content'] ?? '');
        if (strlen($content) < 20) {
            Session::flash('error', 'Please write at least 20 characters for your testimonial.');
            Helper::redirect('/member/profile');
        }

        $attendee = Registration::getAttendeeProfile((int)$attendeeId);
        \Models\Testimonial::create([
            'attendee_id' => $attendeeId,
            'author_name' => ($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? ''),
            'content'     => $content,
            'rating'      => min(5, max(1, (int)($_POST['rating'] ?? 5))),
            'is_approved' => 0,
        ]);

        Session::flash('success', 'Thank you! Your testimonial will appear after admin approval.');
        Helper::redirect('/member/profile');
    }
}
