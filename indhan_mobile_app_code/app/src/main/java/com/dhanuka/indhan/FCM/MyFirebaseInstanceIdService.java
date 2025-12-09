//package com.dhanuka.indhan.FCM;
//
//
//import android.util.Log;
//
//import com.dhanuka.indhan.Preference.Preference;
//import com.google.firebase.iid.FirebaseInstanceId;
//import com.google.firebase.iid.FirebaseInstanceIdService;
//
///**
// * Created by John on 20/8/2018.
// */
//
////the class extending FirebaseInstanceIdService
//public class MyFirebaseInstanceIdService extends FirebaseInstanceIdService {
//    Preference preference;
//
//    //this method will be called
//    //when the token is generated
//    @Override
//    public void onTokenRefresh() {
//        super.onTokenRefresh();
//        preference = new Preference(this);
//        //now we will have the token
//        String token = FirebaseInstanceId.getInstance().getToken();
//        preference.putString("fb", token);
//
//        //for now we are displaying the token in the log
//        //copy it as this method is called only when the new token is generated
//        //and usually new token is only generated when the app is reinstalled or the data is cleared
//        Log.d("MyRefreshedToken", token);
//    }
//}
