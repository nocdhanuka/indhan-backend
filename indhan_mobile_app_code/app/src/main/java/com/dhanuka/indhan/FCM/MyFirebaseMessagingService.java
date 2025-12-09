package com.dhanuka.indhan.FCM;


import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.widget.RemoteViews;

import androidx.annotation.NonNull;
import androidx.core.app.NotificationCompat;

import com.dhanuka.indhan.MenuScreenActivity.View.DynamicMenuActivity;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.R;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;


/**
 * Created by John on 20/8/2018.
 */

//class extending FirebaseMessagingService
public class MyFirebaseMessagingService extends FirebaseMessagingService {
    private RemoteViews mContentViewSmall;
    Preference preference;

    @Override
    public void onNewToken(@NonNull String token) {
        super.onNewToken(token);
        preference = new Preference(this);
        preference.putString("fb", token);
    }

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);


        if (remoteMessage.getData().size() > 0) {
            //handle the data message here
        }
        //getting the title and the body
        String title = remoteMessage.getNotification().getTitle();
        String body = remoteMessage.getNotification().getBody();

        // Set the info for the views that show in the notification panel.

       /* Notification notification = new NotificationCompat.Builder(this)
                .setSmallIcon(R.drawable.ic_notification_app_logo)  // the status icon
                .setWhen(System.currentTimeMillis())  // the time stamp
                .setContentIntent(contentIntent)  // The intent to send when the entry is clicked
               .setCustomContentView( getSmallContentView())
               .setCustomBigContentView(getSmallContentView())
                .setPriority(NotificationCompat.PRIORITY_MAX)
                .setOngoing(true)
                .build();*/
        if (title != null)
            if (title.isEmpty())
                title = "Indhan Alert";
        if (body != null)
            if (body.isEmpty())
                body = "Indhan Alert";

        createNotification(title, body);
        // Send the notification.
        // startForeground(2, notification);
        //then here we can use the title and body to build a notification
    }

    private void createNotification(String title, String messageBody) {
        Intent intent = new Intent(this, DynamicMenuActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent resultIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_ONE_SHOT);
        Uri notificationSoundURI = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        NotificationCompat.Builder mNotificationBuilder = new NotificationCompat.Builder(this)
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentTitle(title)
                .setContentText(messageBody)
                .setAutoCancel(true)
                .setSound(notificationSoundURI)
                .setContentIntent(resultIntent);
        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        notificationManager.notify(2, mNotificationBuilder.build());
    }

}
