package com.dhanuka.indhan.Activity;

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
import android.os.Handler;
import android.text.TextUtils;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.view.WindowManager;
import android.widget.Toast;

import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.R;
import com.dhanuka.indhan.View.CustomProgressDialog;

import org.greenrobot.eventbus.EventBus;
import org.greenrobot.eventbus.Subscribe;
import org.greenrobot.eventbus.ThreadMode;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Created by Wildnet Technologies on 12/28/2016.
 */
public abstract class BaseActivity extends ParentActivity implements View.OnClickListener {

    public Activity activity;
    IntentFilter filter = new IntentFilter(ConnectivityManager.CONNECTIVITY_ACTION);
    private Toast toast;
    private CustomProgressDialog progressDialog;
    private NetworkChangeReceiver receiver;
    private boolean isConnected = false;
    private ViewGroup rootLayout;
    private boolean keyboardListenersAttached = false;
    boolean isUploadedApi = false;
    private final Handler handler = new Handler();
    private final Runnable runnable = () -> {
        isUploadedApi = false;
    };

    private final ViewTreeObserver.OnGlobalLayoutListener keyboardLayoutListener = new ViewTreeObserver.OnGlobalLayoutListener() {
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
        if (!((Activity) activity).isFinishing() &&
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

    // validating email
    public boolean isValidEmail(String email) {
        String EMAIL_PATTERN = "^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
                + "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
        Pattern pattern = Pattern.compile(EMAIL_PATTERN);
        Matcher matcher = pattern.matcher(email);
        return matcher.matches();
    }

    // validating phone
    public boolean isValidPhone(String phone) {
        if (phone.length() != 10) {
            return false;
        } else {
            return android.util.Patterns.PHONE.matcher(phone).matches();
        }
    }

    // validating password with retype password
    public boolean isValidPassword(String pass) {
        Pattern PASSWORD_PATTERN = Pattern.compile("[a-zA-Z0-9+_.]{4,16}");
        return pass != null && pass.length() >= 6;
    }

    public boolean isValidEmpId(String emp) {
        return emp != null && emp.length() >= 1;
    }

    // hide SoftKeyboard
    public void hideSoftKeyboard() {
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
    }

    protected void onShowKeyboard(int keyboardHeight) {
    }

    protected void onHideKeyboard() {

    }

    protected void attachKeyboardListeners() {
        if (keyboardListenersAttached) {
            return;
        }

        rootLayout = (ViewGroup) findViewById(R.id.rootLayout);
        rootLayout.getViewTreeObserver().addOnGlobalLayoutListener(keyboardLayoutListener);

        keyboardListenersAttached = true;
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (progressDialog != null)
            if (progressDialog.isShowing())
                progressDialog.dismiss();
        progressDialog = null;
        handler.removeCallbacks(runnable);
        if (keyboardListenersAttached) {
            rootLayout.getViewTreeObserver().removeGlobalOnLayoutListener(keyboardLayoutListener);
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        unregisterReceiver(receiver);
    }

    public void getClosedTicketList(Context context) {
        if (!isUploadedApi) {
            isUploadedApi = true;
            // 10 Second
            int interval = 10000;
            handler.postDelayed(runnable, interval);
            OfflineDatabase offlineDatabase = new OfflineDatabase(this.getApplicationContext());
            String Token = Preference.getInstance(this).getString("token");
            offlineDatabase.getOnlineClosedTicket(Token);
            offlineDatabase.getOfflineCreatedTicket(this.getApplicationContext());
            offlineDatabase.getPlannedClosedTicket(Token);
            offlineDatabase.getPlannedTicketList(Token);
            offlineDatabase.inviteFarmer(Token);
            offlineDatabase.getIfcFarmerData();
            offlineDatabase.uploadNonUploadedImages(context);
            offlineDatabase.getOfflineFarmer();
            offlineDatabase.getMarkVisitList();
            offlineDatabase.uploadManualTicketData();
            offlineDatabase.uploadManualSelfParticipationTicketData();
            offlineDatabase.uploadArrivalDepartureData();
            offlineDatabase.callApiUpdateTeaEstate();
            offlineDatabase.uploadRequestMaterialData();
            offlineDatabase.uploadArrivalDepartureAtpTeaEstateData();
            offlineDatabase.uploadArrivalDepartureAtpSugarMillData();
            offlineDatabase.uploadArrivalDepartureAtpInfluencerData();
            offlineDatabase.uploadArrivalDepartureDistributorData();
            offlineDatabase.callApiUpdateSugarMill();
            offlineDatabase.callApiUpdateInfluencers();
        }
    }

    public void uploadOfflineDemoRequest() {
        OfflineDatabase offlineDatabase = new OfflineDatabase(this.getApplicationContext());
        offlineDatabase.getOfflineDemoRequest();
    }

    public class NetworkChangeReceiver extends BroadcastReceiver {

        @Override
        public void onReceive(final Context context, final Intent intent) {
            isNetworkAvailable(context);
        }
    }

    public void isNetworkAvailable(Context context) {
        ConnectivityManager connectivity = (ConnectivityManager)
                context.getSystemService(Context.CONNECTIVITY_SERVICE);
        if (connectivity != null) {
            NetworkInfo[] info = connectivity.getAllNetworkInfo();
            if (info != null) {
                for (NetworkInfo networkInfo : info) {
                    if (networkInfo.getState() == NetworkInfo.State.CONNECTED) {
                        if (!isConnected) {
                            isConnected = true;
                            getClosedTicketList(context);
                        }
                        return;
                    }
                }
            }
        }
        isConnected = false;
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public void onMessageEvent(String event) {
        if (!TextUtils.isEmpty(event)) {
            if ("logout".equals(event)) {
                makeToast("Token expired please log in again.");
                Preference pref = new Preference(BaseActivity.this);
                pref.putString("ticketId", "");
                pref.putString("ticketTime", "");
                pref.putBolean("isLogin", false);
                pref.putString("token", "");
                launchIntent(LoginActivity.class, false);
                finishAffinity();
            }
        }
    }

    @Override
    protected void onStop() {
        super.onStop();
        EventBus.getDefault().unregister(this);
    }


    @Override
    protected void onStart() {
        super.onStart();
        if (!EventBus.getDefault().isRegistered(this))
            EventBus.getDefault().register(this);
    }

}