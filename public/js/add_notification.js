const notificationContainer = document.getElementById("notification_list");
function checkNotificationCount(count) {
    if (count !== 0) {
        fetch_top_three_notifications();
        $("#notification_list").show();
        $("#no_notifications").hide();
    } else {
        $("#notification_list").hide();
        $("#no_notifications").show();
    }
}
checkNotificationCount(notification_count);

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
                    let processedNotification =
                        transformNotificationResponse(notification);
                    addIncomingNotification(processedNotification);
                    $("#notification_list").show();
                    $("#no_notifications").hide();
                });
            }
        },
        error: function (jqXhr, textStatus, errorThrown) {},
    });
}

Echo.private(`App.Models.User.${userId}`).notification((notification) => {
    addIncomingNotification(notification);
    $("#notification_list").show();
    $("#no_notifications").hide();
    feedback(`New notification on ${notification.notificationType}`, "warning");
});

function addIncomingNotification(notification) {
    const actionLink = notification.actionLink
        ? ` <a href="${notification.actionLink}">link</a>`
        : "";
    let elementContent = `
        <div">
            <div class="align-items-center">
                <div class="toast-body mx-2">
                    <h4 class="header-notification">${notification.notificationTitle}</h4>
                    <p class="text-notification mb-0 truncate-fade">${notification.notificationMessage}${actionLink}</p>
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

    if (notification.notificationsCount) {
        document.getElementById("notification_count").textContent =
            notification.notificationsCount;
    }

    length > 0
        ? childrenElements[0].insertAdjacentElement("beforebegin", newElement)
        : notificationContainer.appendChild(newElement);
}

function markAsRead(notificationId) {
    const url = markAsReadUrl + "/" + notificationId;
    $.ajax({
        url: url,
        method: "get",
        headers: {
            Accept: "application/json",
        },
        success: function (data, textStatus, jQxhr) {
            const notification = document.getElementById(
                `not-${notificationId}`
            );
            notification.remove();
            let { notificationCount, topNotifications } = data;
            document.getElementById("notification_count").textContent =
                notificationCount;

            replaceNotifications(topNotifications);

            if (notificationCount !== 0) {
                $("#notification_list").show();
                $("#no_notifications").hide();
            } else {
                $("#notification_list").hide();
                $("#no_notifications").show();
            }
        },
        error: function (jqXhr, textStatus, errorThrown) {},
    });
}

function replaceNotifications(notifications) {
    const existingNotifications =
        document.getElementById("notification_list").children;

    const length = existingNotifications.length;
    const existingNotificationsIds = [];
    for (counter = 0; counter < length; counter++) {
        const id = existingNotifications[counter].id;
        existingNotificationsIds.push(id.slice(4));
    }

    const notificationsToAdd = notifications.filter(
        (notification) => !existingNotificationsIds.includes(notification.id)
    );

    notificationsToAdd.forEach((notification) => {
        const processedNotification =
            transformNotificationResponse(notification);
        addIncomingNotification(processedNotification);
    });
}

function transformNotificationResponse(notification) {
    let data = JSON.parse(notification.data);

    let processedNotification = {
        createdAt: notification.created_at,
        id: notification.id,
        notificationTitle: data.notification_title,
        notificationMessage: data.notification_message,
        actionLink: data.action_link,
    };

    return processedNotification;
}
