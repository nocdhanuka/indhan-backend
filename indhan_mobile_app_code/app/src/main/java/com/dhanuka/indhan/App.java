package com.dhanuka.indhan;

import static android.content.ContentValues.TAG;

import android.app.Application;
import android.content.Context;
import android.content.IntentFilter;
import android.net.ConnectivityManager;
import android.util.Log;

import androidx.multidex.MultiDex;

import com.dhanuka.indhan.DataSyncServices.ConnectivityChangeReceiver;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.Retrofit.NetworkClientRx;
import com.dhanuka.indhan.analytics.FirebaseAnalyticsUtil;
import com.downloader.PRDownloader;
import com.google.firebase.crashlytics.FirebaseCrashlytics;
import com.orhanobut.logger.AndroidLogAdapter;
import com.orhanobut.logger.Logger;

import ren.yale.android.retrofitcachelibrx2.RetrofitCache;


public class App extends Application {
    private ConnectivityChangeReceiver connectivityReceiver;

    public static Context appContext;

    private ConnectivityChangeReceiver getConnectivityReceiver() {
        if (connectivityReceiver == null)
            connectivityReceiver = new ConnectivityChangeReceiver();
        return connectivityReceiver;
    }

    @Override
    public void onCreate() {
        super.onCreate();
        FirebaseCrashlytics.getInstance().setCrashlyticsCollectionEnabled(!BuildConfig.DEBUG);
        appContext = this;
        NetworkClientRx.INSTANCE.init(this);
        Preference.getInstance(this);
        RetrofitCache.getInstance().init(this).enableMock(false);
        if (BuildConfig.DEBUG) {
            Logger.addLogAdapter(new AndroidLogAdapter());
        }
        initAnalytics();
        PRDownloader.initialize(getApplicationContext());
        registerConnectivityReceiver();
    }

    protected void initAnalytics() {
        FirebaseAnalyticsUtil.initialize(this);
    }

    @Override
    public void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        try {
            MultiDex.install(this);
        } catch (RuntimeException multiDexException) {
            multiDexException.printStackTrace();
        }
    }

    private void registerConnectivityReceiver() {
        try {
            IntentFilter filter = new IntentFilter();
            filter.addAction(ConnectivityManager.CONNECTIVITY_ACTION);
            registerReceiver(getConnectivityReceiver(), filter);
        } catch (Exception e) {
            Log.e(TAG, e.getMessage());
        }
    }
}
