const notificationContainer = document.getElementById("notification_list");
const noNotificationElement = document.getElementById("no_notifications");

function checkNotificationCount(count) {
    if (count !== 0) {
        fetchTopNotifications();
        notificationContainer.style.display = "block";
        noNotificationElement.style.display = "";
    } else {
        notificationContainer.style.display = "";
        noNotificationElement.style.display = "block";
    }
}
checkNotificationCount(notification_count);

async function fetchTopNotifications() {
    if (notificationsRoute == null || notificationsRoute === "") return;

    try {
        const response = await fetch(notificationsRoute);

        const responseBody = await response.json();

        if (!response.ok) {
            simpleToast.toast(
                "An error seems to have occurred when getting your latest notifications. The service will be temporarily unavailable.",
                "error"
            );
            return;
        }

        const { notifications } = responseBody;

        notifications.forEach((notification) => {
            const processedNotification =
                transformNotificationResponse(notification);
            addIncomingNotification(processedNotification);
        });

        if (notifications.length > 0) {
            notificationContainer.style.display = "block";
            noNotificationElement.style.display = "";
        }
    } catch (error) {
        console.log(error);
        simpleToast.toast(
            "An error seems to have occurred. Please try again later",
            "error"
        );
    }
}

Echo.private(`App.Models.User.${userId}`).notification((notification) => {
    addIncomingNotification(notification);
    notificationContainer.style.display = "block";
    noNotificationElement.style.display = "";

    simpleToast.toast(
        `New notification on ${notification.notificationType}`,
        "warning"
    );
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

async function markAsRead(notificationId) {
    const url = markAsReadUrl + "/" + notificationId;

    try {
        const response = await fetch(url);

        const responseBody = await response.json();

        if (!response.ok) {
            simpleToast.toast(
                "An error seems to have occurred marking the notification as read. The service will be temporarily unavailable.",
                "error"
            );
            return;
        }

        const notification = document.getElementById(`not-${notificationId}`);
        notification.remove();

        let { notificationCount, topNotifications } = responseBody;
        document.getElementById("notification_count").textContent =
            notificationCount;

        replaceNotifications(topNotifications);

        if (notificationCount > 0) {
            notificationContainer.style.display = "block";
            noNotificationElement.style.display = "";
        } else {
            notificationContainer.style.display = "";
            noNotificationElement.style.display = "block";
        }
    } catch (error) {
        console.log(error);
        simpleToast.toast(
            "An error seems to have occurred. Please try again later",
            "error"
        );
    }
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
