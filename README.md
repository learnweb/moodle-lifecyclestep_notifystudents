# NotifyStudents (moodle-lifecyclestep_notifystudents)

This is a step for the [Life Cycle](https://github.com/learnweb/moodle-tool_lifecycle) Plugin, which offers the option to notify enrolled students of the courses in the workflow. 

Before notification, the editingteachers can decide whether to opt-in or opt-out of notification depending on the option the admin chose beforehand.

## Step Setup
When a notifystudents step is added to a workflow the admin has multiple setup options:
- Time to respond
- Notification Option: Opt-In or Opt-Out
- E-Mail Text and Subject for the teachers and the students

<img src="https://user-images.githubusercontent.com/74201118/160656948-b83fa766-40fa-4080-8f4e-155b98952553.png" width=80%>
<img src=https://user-images.githubusercontent.com/74201118/160657125-343ebbb8-640d-4459-a81b-ad9b3635e8a8.png width=80%>

## Behavior
When a cron-job is executed and a course is triggered by the notifystudents step, all editingteachers of said course are being notified with the preconfigured email text and subject. The teacher can then take action on the "Manage Course" page and decide whether the enrolled students should or shouldn't be notified.

#### Opt-In
If an editing teacher takes action all enrolled students are immediately notified regarding only this particular course. When no action is taken, the students are not getting an email when the next cron-job is executed.

#### Opt-Out
If an editing teacher takes action all enrolled students are excluded from notification regarding only this particular course. When no action is taken, the students are receiving an email with all courses from this workflow they are enrolled in.
