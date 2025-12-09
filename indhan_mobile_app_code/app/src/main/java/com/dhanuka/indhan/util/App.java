package com.dhanuka.indhan.util;

import android.app.Application;
import android.util.Log;

import androidx.annotation.NonNull;

import com.dhanuka.indhan.Retrofit.NetworkClientRx;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.remoteconfig.FirebaseRemoteConfig;
import com.orhanobut.logger.AndroidLogAdapter;
import com.orhanobut.logger.Logger;

import ren.yale.android.retrofitcachelibrx2.RetrofitCache;

public class App extends Application {
    @Override
    public void onCreate() {
        super.onCreate();
        NetworkClientRx.INSTANCE.init(this);
        RetrofitCache.getInstance().init(this).enableMock(false);
        Logger.addLogAdapter(new AndroidLogAdapter());
        final FirebaseRemoteConfig firebaseRemoteConfig = FirebaseRemoteConfig.getInstance();
//		Map<String, Object> remoteConfigDefaults = new HashMap();
//		remoteConfigDefaults.put(ForceUpdateChecker.KEY_UPDATE_REQUIRED, false);
//		remoteConfigDefaults.put(ForceUpdateChecker.KEY_CURRENT_VERSION, "1.4.3");
//		remoteConfigDefaults.put(ForceUpdateChecker.KEY_UPDATE_URL, "https://play.google.com/store/apps/details?id=com.dhanuka.indhan");
//		FirebaseRemoteConfigSettings settitng = new FirebaseRemoteConfigSettings.Builder()
//				.setDeveloperModeEnabled(false)
//				.build();
//		firebaseRemoteConfig.setConfigSettings(settitng);
//		firebaseRemoteConfig.setDefaults(remoteConfigDefaults);

        firebaseRemoteConfig.fetch(60 * 60 * 2)// fetch every minutes
                .addOnCompleteListener(new OnCompleteListener<Void>() {
                    @Override
                    public void onComplete(@NonNull Task<Void> task) {
                        if (task.isSuccessful()) {
                            Log.d("Vinod", "remote config is fetched.");
                            firebaseRemoteConfig.fetchAndActivate();
                        }
                    }
                });
    }
}
