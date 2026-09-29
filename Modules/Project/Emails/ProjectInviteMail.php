<?php

namespace Modules\Project\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Setting\Model\EmailTemplate;
use Auth;

class ProjectInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user, $project, $authUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $project,$authUser)
    {
        $this->user = $user;
        $this->project = $project;
        $this->authUser = $authUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $template = EmailTemplate::where('type', 'project_member_invitation')->first();

        $subject = $template->subject;
        $body = $template->value;
        
        $name = $this->user->name ?? $this->user->email;
        $authUser = $this->authUser->name ?? $this->authUser->email;

        // Generate project URL
        $projectUrl = route('project.show', $this->project->uuid);

        // Create a styled button/link
        $projectLink = "<a href=\"$projectUrl\" style=\"color: green;\">View Project</a>";

        $key = [
            '{INVITATION_SENT_BY}',
            '{USER_NAME}',
            '{PROJECT_NAME}',
            'http://{PROJECT_URL}', // Remove hardcoded prefix
            '{EMAIL_SIGNATURE}',
            '{EMAIL_FOOTER}',
            '{PROJECT_URL}', // For dynamic replacement
        ];

        $value = [
            $authUser,
            $name,
            $this->project->name,
            $projectLink, // Replace with styled button or link
            config('config.mail_signature'),
            config('config.mail_footer'),
            $projectLink, // Ensures consistent replacement
        ];

        $body = str_replace($key, $value, $body);

        return $this->view('project::mail.projectinvite')
                    ->with(['body' => $body])
                    ->subject($subject);
    }

}
