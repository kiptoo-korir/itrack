const notificationContainer = document.getElementById("notification_list");
if (notification_count !== 0) {
    fetch_top_three_notifications();
    $("#notification_list").show();
    $("#no_notifications").hide();
} else {
    $("#notification_list").show();
    $("#no_notifications").hide();
}

function fetch_top_three_notifications() {
    $.ajax({
        url: notificationsRoute,
        method: "get",
        headers: {
            Accept: "application/json",
        },
        success: function (data, textStatus, jQxhr) {
            let notifications = data.notifications;
            if (notifications.length > 0) {
                notifications.forEach((notification) => {
                    let data = JSON.parse(notification.data);
                    let processedNotification = {
                        created_at: notification.created_at,
                        id: notification.id,
                        notification_title: data.notification_title,
                        notification_message: data.notification_message,
                    };
                    add_incoming_notification(processedNotification);
                });
            }
        },
        error: function (jqXhr, textStatus, errorThrown) {},
    });
}

Echo.private(`App.Models.User.${userId}`).notification((notification) => {
    add_incoming_notification(notification);
    feedback(
        `New notification on ${notification.notification_type}`,
        "warning"
    );
});

function add_incoming_notification(notification) {
    let elementContent = `
        <div>
            <div class="align-items-center">
                <div class="toast-body mx-2">
                    <h4 class="header-notification">${notification.notification_title}</h4>
                    <p class="text-notification mb-0 truncate-fade">${notification.notification_message}</p>
                    <div class="text-right text-notification"><a class="mark-as-read"
                            href="javascript:void(0)"
                            onclick="markAsRead('${notification.id}')">Mark as read</a></div>
                </div>
            </div>
        </div>
    `;
    let newElement = document.createElement("div");
    newElement.id = `not-${notification.id}`;
    newElement.classList.add("list-group", "list-group-flush");
    newElement.innerHTML = elementContent;
    let childrenElements = notificationContainer.children;
    let length = childrenElements.length;

    if (length > 2) {
        notificationContainer.removeChild(childrenElements[length - 1]);
    }

    if (notification.notifications_count) {
        document.getElementById("notification_count").textContent =
            notification.notifications_count;
    }

    length > 0
        ? childrenElements[0].insertAdjacentElement("beforebegin", newElement)
        : notificationContainer.appendChild(newElement);
}

function markAsRead(notificationId) {
    console.log(notificationId);
    console.log(document.getElementById(`not-${notificationId}`));
}
