package com.dhanuka.indhan.DataSyncServices;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;

import com.dhanuka.indhan.util.CheckConnectionStatus;

public class ConnectivityChangeReceiver extends BroadcastReceiver {


    @Override
    public void onReceive(Context context, Intent intent) {

        if (CheckConnectionStatus.isInternetAvailable(context)) {
            Intent intent1 = new Intent(context, DataSyncServices.class);
            if (intent != null) {
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                    context.startForegroundService(intent1);
                } else {
                    context.startService(intent1);
                }
            }
        }
    }


}