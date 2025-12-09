package com.dhanuka.indhan.Activity

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent

import com.dhanuka.indhan.util.Utility

/**
 * Created by Shivam on 3/13/2018.
 */

class MyStartServiceReceiver : BroadcastReceiver() {

    override fun onReceive(context: Context, intent: Intent) {
        Utility.scheduleJob(context)
    }
}
