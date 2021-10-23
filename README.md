# NotifyStudents (moodle-lifecyclestep_notifystudents)

This is a step for the [Life Cycle](https://github.com/learnweb/moodle-tool_lifecycle) Plugin, which offers the option to notify enrolled students of the courses in the workflow. 

Before notification, the editingteachers can decide whether to opt-in or opt-out of notification depending on the option the admin chose beforehand.

## Step Setup
When a notifystudents step is added to a workflow the admin has multiple setup options:
- Time to respond
- Notification Option: Opt-In or Opt-Out
- E-Mail Text and Subject for the teachers and the students

## Behavior
When a cron-job is executed and a course is triggered by the notifystudents step, all editingteachers of said course are being notified with the preconfigured email text and subject. The teacher can then take action on the "Manage Course" page and decide whether the enrolled students should or shouldn't be notified.

#### Opt-In
If an editing teacher takes action all enrolled students are immediately notified regarding only this particular course. When no action is taken, the students are not getting an email when the next cron-job is executed.

#### Opt-Out
If an editing teacher takes action all enrolled students are excluded from notification regarding only this particular course. When no action is taken, the students are receiving an email with all courses from this workflow they are enrolled in.
