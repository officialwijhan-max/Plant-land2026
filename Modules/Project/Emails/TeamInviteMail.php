<?php

namespace Modules\Project\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Setting\Model\EmailTemplate;
use Auth;
class TeamInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $team,$authUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $team, $authUser)
    {
        $this->user = $user;
        $this->team = $team;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = EmailTemplate::where('type', 'team_member_invitation')->first();

        $subject = $template->subject;
        $body = $template->value;
        
        $name = $this->user->name ?? $this->user->email;
        $authUser = $this->authUser->name ?? $this->authUser->email;
        $key = ['{INVITATION_SENT_BY}', '{USER_NAME}', '{TEAM_NAME}', '{TEAM_URL}', '{EMAIL_SIGNATURE}', '{EMAIL_FOOTER}'];
        
        $teamUrl = route('team.show', $this->team->id); // Generate the URL
        $teamLink = "<a href=\"$teamUrl\" style=\"color: green;\">Accept Invitation</a>";

        $value = [
            $authUser,
            $name,
            $this->team->name,
            $teamLink, // Replace with styled button or link
            app('general_setting')->mail_signature,
            app('general_setting')->mail_footer
        ];
        
        $body = str_replace($key, $value, $body);

        return $this->view('project::mail.teaminvite')->with(["body" => $body])->subject($subject);
    }

}
