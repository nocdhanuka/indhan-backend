package com.dhanuka.indhan.MenuScreenActivity;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.ActivityInfo;
import android.graphics.Rect;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.R;
import com.dhanuka.indhan.View.CustomProgressDialog;

/**
 * Created by Wildnet Technologies on 12/28/2016.
 */
public abstract class DynamicMenuBaseActivity extends AppCompatActivity
        implements View.OnClickListener {

    private static final String LOG_TAG = "CheckNetworkStatus";
    public Activity activity;
    IntentFilter filter = new IntentFilter(ConnectivityManager.CONNECTIVITY_ACTION);
    private View v;
    private Toast toast;
    private CustomProgressDialog progressDialog;
    private NetworkChangeReceiver receiver;
    private boolean isConnected = false;
    private ViewGroup rootLayout;
    private boolean keyboardListenersAttached = false;
    private ViewTreeObserver.OnGlobalLayoutListener keyboardLayoutListener = new ViewTreeObserver.OnGlobalLayoutListener() {
        @Override
        public void onGlobalLayout() {
            // navigation bar height
            int navigationBarHeight = 0;
            int resourceId = getResources().getIdentifier("navigation_bar_height", "dimen", "android");
            if (resourceId > 0) {
                navigationBarHeight = getResources().getDimensionPixelSize(resourceId);
            }

            // status bar height
            int statusBarHeight = 0;
            resourceId = getResources().getIdentifier("status_bar_height", "dimen", "android");
            if (resourceId > 0) {
                statusBarHeight = getResources().getDimensionPixelSize(resourceId);
            }

            // display window size for the app layout
            Rect rect = new Rect();
            getWindow().getDecorView().getWindowVisibleDisplayFrame(rect);

            // screen height - (user app height + status + nav) ..... if non-zero, then there is a soft keyboard
            int keyboardHeight = rootLayout.getRootView().getHeight() - (statusBarHeight + navigationBarHeight + rect.height());

            if (keyboardHeight <= 0) {
                onHideKeyboard();
            } else {
                onShowKeyboard(keyboardHeight);
            }
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
        overridePendingTransition(R.anim.fade_in, R.anim.fade_out);
        activity = this;
        v = getWindow().getDecorView().getRootView();
        progressDialog = new CustomProgressDialog(activity);
        receiver = new NetworkChangeReceiver();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (!activity.isFinishing())
            registerReceiver(receiver, filter);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);
    }

    public void showProgress() {

        if (!activity.isFinishing()) {

            if (progressDialog == null) {
                progressDialog = new CustomProgressDialog(activity);
                progressDialog.show();
            } else {
                if (!progressDialog.isShowing()) {
                    progressDialog.show();
                }
            }
        }
    }

    public void dismissProgress() {
        if (!activity.isFinishing() &&
                progressDialog != null &&
                progressDialog.isShowing()) {
            progressDialog.dismiss();
        }
    }

    public void makeToast(String message) {
        if (toast == null) {
            toast = Toast.makeText(getApplicationContext(), message, Toast.LENGTH_SHORT);
            toast.setGravity(Gravity.BOTTOM, 0, 10);
        }
        if (toast != null) {
            if (message.length() > 120) {
                toast.setDuration(Toast.LENGTH_LONG);
            }
            toast.setText(message);
            toast.show();
        }
    }

    public void launchIntent(Class<? extends Activity> cls, boolean finish) {
        Intent intent = new Intent(activity, cls);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        startActivity(intent);
        if (finish)
            finish();
    }

    public void launchIntent(Class<? extends Activity> cls, Bundle bundle, boolean finish) {
        Intent intent = new Intent(activity, cls);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intent.putExtras(bundle);
        startActivity(intent);
        if (finish)
            finish();
    }

    public void hideProgress() {
        if (!(activity).isFinishing() &&
                progressDialog != null &&
                progressDialog.isShowing()) {
            progressDialog.dismiss();

        }
    }

    protected void onShowKeyboard(int keyboardHeight) {

    }

    protected void onHideKeyboard() {

    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (progressDialog != null)
            if (progressDialog.isShowing())
                progressDialog.dismiss();
        progressDialog = null;

        if (keyboardListenersAttached) {
            rootLayout.getViewTreeObserver().removeGlobalOnLayoutListener(keyboardLayoutListener);
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        unregisterReceiver(receiver);
    }

    private void getClosedTicketList(Context context) {
        OfflineDatabase offlineDatabase = new OfflineDatabase(this.getApplicationContext());
        String Token = Preference.getInstance(this).getString("token");
        offlineDatabase.getOnlineClosedTicket(Token);
        offlineDatabase.getOfflineCreatedTicket(this.getApplicationContext());
        offlineDatabase.getPlannedClosedTicket(Token);
        offlineDatabase.getPlannedTicketList(Token);
        offlineDatabase.inviteFarmer(Token);
        offlineDatabase.getMarkVisitList();
        offlineDatabase.getIfcFarmerData();
        offlineDatabase.uploadNonUploadedImages(context);
        offlineDatabase.uploadManualTicketData();
        offlineDatabase.uploadManualSelfParticipationTicketData();
        offlineDatabase.uploadArrivalDepartureData();
        offlineDatabase.callApiUpdateTeaEstate();
        offlineDatabase.callApiUpdateSugarMill();
        offlineDatabase.callApiUpdateInfluencers();
        offlineDatabase.uploadRequestMaterialData();
        offlineDatabase.uploadArrivalDepartureAtpTeaEstateData();
        offlineDatabase.uploadArrivalDepartureDistributorData();
        offlineDatabase.uploadArrivalDepartureAtpSugarMillData();
        offlineDatabase.uploadArrivalDepartureAtpInfluencerData();
    }

    public class NetworkChangeReceiver extends BroadcastReceiver {

        @Override
        public void onReceive(final Context context, final Intent intent) {
            isNetworkAvailable(context);
        }


        private boolean isNetworkAvailable(Context context) {
            ConnectivityManager connectivity = (ConnectivityManager)
                    context.getSystemService(Context.CONNECTIVITY_SERVICE);
            if (connectivity != null) {
                NetworkInfo[] info = connectivity.getAllNetworkInfo();
                if (info != null) {
                    for (int i = 0; i < info.length; i++) {
                        if (info[i].getState() == NetworkInfo.State.CONNECTED) {
                            if (!isConnected) {
                                Log.v(LOG_TAG, "Now you are connected to Internet!");
                                isConnected = true;
                                getClosedTicketList(context);
                            }
                            return true;
                        }
                    }
                }
            }
            Log.v(LOG_TAG, "You are not connected to Internet!");
            isConnected = false;
            return false;
        }
    }
}