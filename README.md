# NotifyStudents (moodle-lifecyclestep_notifystudents)

This is a step for the [Life Cycle](https://github.com/learnweb/moodle-tool_lifecycle) Plugin, which offers the option to notify enrolled students of the courses in the workflow. 

Before notification, the editingteachers can decide whether to opt-in or opt-out of notification depending on the option the admin chose beforehand.

## Step Setup
When a notifystudents step is added to a workflow the administrator has multiple setup options:
- Time to respond
- Notification Option: Opt-In or Opt-Out
- E-Mail Text and Subject for the teachers and the students

<img src="https://user-images.githubusercontent.com/74201118/160656948-b83fa766-40fa-4080-8f4e-155b98952553.png" width=80%>
<img src=https://user-images.githubusercontent.com/74201118/160657125-343ebbb8-640d-4459-a81b-ad9b3635e8a8.png width=80%>

## Behavior
When a cron-job is executed and a course is triggered by the notifystudents step, all editingteachers of said course are being notified with the preconfigured email text and subject. The teacher can then take action on the "Manage Course" page and decide whether the enrolled students should or shouldn't be notified. If one of the two options is picked the action is triggered immediately. The Teacher can also take no action and the preconfigured option will trigger at the execution of the next cron-job.

<img src=https://user-images.githubusercontent.com/74201118/160929784-51349be0-9d16-4874-ac42-e070a12ab365.png width=80%>

#### Opt-In
When no action is taken, the students are not getting an email when the next cron-job is executed.

#### Opt-Out
When no action is taken, the students are receiving an email with all courses from this workflow they are enrolled in.
