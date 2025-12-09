package com.dhanuka.indhan.RegisterFarmerScreen.normalFarmerRegistration;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.Gravity;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.dhanuka.indhan.Activity.AddNumberActivity;
import com.dhanuka.indhan.Activity.BaseActivity;
import com.dhanuka.indhan.Activity.LoginActivity;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.RegisterFarmersContracts;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.RegisterFarmersPresenterImpl;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersPresenterImpl;
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel;
import com.dhanuka.indhan.DataBase.Contact;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Model.AddNumberModel;
import com.dhanuka.indhan.Model.CommonModel;
import com.dhanuka.indhan.Model.RegisterFarmerContactModel;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.R;
import com.dhanuka.indhan.RegisterFarmerScreen.SyncContactsToDB;
import com.dhanuka.indhan.View.CustomProgressDialog;
import com.dhanuka.indhan.roomdatabase.AppDatabase;
import com.dhanuka.indhan.roomdatabase.DatabaseClient;
import com.dhanuka.indhan.util.AppLogFile;
import com.dhanuka.indhan.util.CheckConnectionStatus;
import com.dhanuka.indhan.util.LogModel;
import com.dhanuka.indhan.util.Utility;
import com.google.firebase.analytics.FirebaseAnalytics;
import com.google.gson.Gson;

import org.joda.time.DateTime;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;

public class FarmerRegisterActivity extends BaseActivity implements SyncContactsToDB.AsyncPost, SubmitFarmersContracts.ISubmitFarmersView, RegisterFarmersContracts.IRegisterFarmersView, View
        .OnClickListener, CallLogNumberAdapter.ListUpdateListener {
    CallLogNumberAdapter callLogAdapter, manualCallAdapter;
    List<Contact> test;
    RecyclerView rvMissCall, rvManualAdded;
    List<RegisterFarmerContactModel> manualCallList, autoCallLogList;
    DatabaseHandler db;
    LinearLayout ivAddMnNumber, ivBack;
    Button btnRbSubmit;
    RelativeLayout rlLoader;
    Preference preference;
    private RegisterFarmersContracts.RegisterFarmersPresenter mRegisterFarmersPresenter;
    private int requestCodePlan = 101;
    private ArrayList<AddNumberModel> checkNumberModels = new ArrayList<>();
    private SubmitFarmersContracts.SubmitFarmersPresenter submitFarmersPresenter;
    private String Token = "", ticketTime = "", ticketId = "";
    private CustomProgressDialog progressDialog;
    private Toast toast;
    private AppDatabase appDatabase;
    private String ticketNumber;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register_jfms);
        appDatabase = DatabaseClient.getInstance(this).getAppDatabase();
        preference = Preference.getInstance(this);
        rvMissCall = findViewById(R.id.rvMissCall);
        rvManualAdded = findViewById(R.id.rvManualAdded);
        ivAddMnNumber = findViewById(R.id.ivAddMnNumber);
        rlLoader = findViewById(R.id.rlLoader);
        ivBack = findViewById(R.id.ivBack);
        btnRbSubmit = findViewById(R.id.btnRbSubmit);
        btnRbSubmit.setOnClickListener(this);
        ivBack.setOnClickListener(this);
        ivAddMnNumber.setOnClickListener(this);
        manualCallList = new ArrayList<>();
        autoCallLogList = new ArrayList<>();
        progressDialog = new CustomProgressDialog(this);
        db = new DatabaseHandler(this);
        mRegisterFarmersPresenter = new RegisterFarmersPresenterImpl(this);
        Token = Preference.getInstance(this).getString("token");
        submitFarmersPresenter = new SubmitFarmersPresenterImpl(this);
        callLogAdapter = new CallLogNumberAdapter(autoCallLogList, this);
        rvMissCall.setLayoutManager(new LinearLayoutManager(this));
        rvMissCall.setAdapter(callLogAdapter);
        manualCallAdapter = new CallLogNumberAdapter(manualCallList, this);
        rvManualAdded.setLayoutManager(new LinearLayoutManager(this));
        rvManualAdded.setAdapter(manualCallAdapter);

        if (ticketTime != null && !ticketTime.isEmpty()) {
            loadLogs();
        } else {
            if (CheckConnectionStatus.isInternetAvailable(FarmerRegisterActivity.this)) {
                mRegisterFarmersPresenter.onSubmitClick(Token);
            } else {
                new GetAllTicketListFromDB().execute();
            }
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        progressDialog = null;
    }

    @Override
    public void showProgress() {
        showP();
    }

    @Override
    public void hideProgress() {
        hidep();
    }

    @Override
    public void setItems(CommonModel items) {
        hidep();
        if (items.getStatus_code() == 200) {
            logEventAddFarmers("Success : " + items.getMessage());
            makeToast(items.getMessage());
            db.updateSyncStatus();
            finish();
        } else if (items.getStatus_code() == 409) {
            preference.putString("ticketId", "");
            preference.putString("ticketTime", "");
            preference.putBolean("isLogin", false);
            preference.putString("token", "");
            logEventAddFarmers("Error : " + items.getMessage());
            launchIntent(LoginActivity.class, true);
        } else if (items.getStatus_code() == 404) {
            db.deleteAll();
            logEventAddFarmers("Error : " + items.getMessage());
            makeToast(items.getMessage());
            finish();
        } else {
            logEventAddFarmers("Error : " + items.getMessage());
            makeToast(items.getMessage());
        }
    }

    void hidep() {
        if (rlLoader.getVisibility() != View.GONE) {
            rlLoader.setVisibility(View.GONE);
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

    void showP() {
        if (rlLoader.getVisibility() != View.VISIBLE) {
            rlLoader.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public void setItems(RegisterFarmersBean items) {
        hideProgress();
        if (items.getStatus_code() == 200) {
            TextView tvReTicketNoDisplay = findViewById(R.id.tvReTicketNoDisplay);
            TextView tvMeTicketTime = findViewById(R.id.tvMeTicketTime);
            tvReTicketNoDisplay.setText(items.getData().get(0).getTicket_no());
            tvMeTicketTime.setText(items.getData().get(0).getDatetime_format());
            ticketTime = items.getData().get(0).getDatetime();
            ticketNumber = items.getData().get(0).getTicket_no();
            preference.putString("ticketsTime", ticketTime);
            preference.putString("ticketsId", String.valueOf(items.getData().get(0).gettID()));
            preference.putString("ticketsNo", items.getData().get(0).getTicket_no());
            preference.putString("ticketsDateTime", items.getData().get(0).getDatetime_format());
            ticketId = String.valueOf(items.getData().get(0).gettID());
            new GetRegisteredNumberFromDb().execute();
            loadLogs();
        } else if (items.getStatus_code() == 409) {
            Preference preference = Preference.getInstance(this);
            preference.putString("ticketId", "");
            preference.putString("ticketTime", "");
            preference.putBolean("isLogin", false);
            preference.putString("token", "");
            launchIntent(LoginActivity.class, false);
            finishAffinity();
        } else {
            makeToast(items.getMessage());
        }
    }

    private void loadLogs() {
        showP();
        updateManualCall();
        if (ticketTime != null && !ticketTime.isEmpty()) {
        }
    }

    void updateManualCall() {
        showP();
        manualCallList.clear();
        List<RegisterFarmerContactModel> tempCallLog = db.getManualData(Utility.getDatefromFormat(ticketTime));
        if (tempCallLog.size() > 0) {
            SimpleDateFormat formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            Date date1 = null;
            Date date2 = null;
            for (RegisterFarmerContactModel model : tempCallLog) {
                try {
                    String dateFormat = model.getTimestamp();
                    date1 = formatter1.parse(dateFormat);
                    date2 = formatter1.parse(ticketTime);
                    if (date1.after(date2)) {
                        manualCallList.add(model);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    hidep();
                }
            }
        }
        manualCallAdapter.notifyDataSetChanged();
        hidep();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == requestCodePlan && resultCode == 101) {
            if (data.getExtras() != null && data.getSerializableExtra("UNIQUE_NUMBERS") != null) {
                checkNumberModels = (ArrayList<AddNumberModel>) data.getExtras().getSerializable("UNIQUE_NUMBERS");
                for (int i = 0; i < checkNumberModels.size(); i++)
                    new InsertFarmerNumberInDB(checkNumberModels.get(i).getNumber()).execute();

                LogModel model = new LogModel();
                model.setTimeToWrite("\nDate Time : " + DateTime.now().toString());
                String dataValue = "\nTicket No : " + ticketNumber + "\nFarmers Data -> " + new Gson().toJson(checkNumberModels) + "\nFarmers Count : " + checkNumberModels.size();
                model.setApiData(dataValue);
                model.setApiName("\nApi Name : v2/farmerRegistrationSubmit\n | Local Add farmer data");
                String userData = preference.getString("user_id") + "_"
                        + preference.getString("ecn") + "_"
                        + preference.getString("user_type") + "_"
                        + preference.getString("Username") + "_"
                        + preference.getString("designation");
                model.setUserData("\nUSER DATA : -> " + userData);
                AppLogFile.INSTANCE.writeLineInFile(new Gson().toJson(model), preference.getString("ecn"));
            }
        }
    }

    private void notifyAdapter(List<FarmerRegistrationBean> farmerRegistrationBeans) {
        manualCallList.clear();
        for (FarmerRegistrationBean farmerRegistrationBean : farmerRegistrationBeans) {
            RegisterFarmerContactModel registerFarmerContactModel = new RegisterFarmerContactModel();
            registerFarmerContactModel.setPhoneNumber(farmerRegistrationBean.getMobileNumber());
            registerFarmerContactModel.setIsDeleted(farmerRegistrationBean.getIsDeleted());
            registerFarmerContactModel.setIsSync(farmerRegistrationBean.getIsSync());
            registerFarmerContactModel.setStatus(farmerRegistrationBean.getStatus());
            registerFarmerContactModel.setTicketNumber(farmerRegistrationBean.getTicketId());
            registerFarmerContactModel.setTimestamp(farmerRegistrationBean.getTimeStamp());
            registerFarmerContactModel.setType(farmerRegistrationBean.getType());
            manualCallList.add(registerFarmerContactModel);
        }
        manualCallAdapter.notifyDataSetChanged();
    }


    private boolean checkNumberInList(String number) {
        if (autoCallLogList.size() > 0) {
            for (RegisterFarmerContactModel call : autoCallLogList) {
                if (call.getPhoneNumber().contains(number)) {
                    return true;
                }
            }
        }
        if (manualCallList.size() > 0) {
            for (RegisterFarmerContactModel call : manualCallList) {
                if (call.getPhoneNumber().contains(number)) {
                    return true;
                }
            }
        }
        return false;
    }

    private void uploadData() {
        showP();
        logEventAddFarmers("");
        if (CheckConnectionStatus.isInternetAvailable(FarmerRegisterActivity.this)) {
            if (manualCallList.size() > 0) {
                if (CheckConnectionStatus.isInternetAvailable(FarmerRegisterActivity.this)) {
                    preference.putLong("lastFarmerUpdated", System.currentTimeMillis() + 600000);
                    submitFarmersPresenter.onSubmitClickOptimized((ArrayList<RegisterFarmerContactModel>) manualCallList, Token);
                }
            } else {
                hidep();
                makeToast("No new number found!");
            }
        } else {
            makeToast("No Internet Connection. Data will be synced when online.");
            hidep();
            finish();
            new updateFarmerRegisteredInDb().execute();
        }
    }

    private void logEventAddFarmers(String s) {
        LogModel model = new LogModel();
        model.setTimeToWrite("\nDate Time : " + DateTime.now().toString());
        String data = "\nTicket No : " + ticketNumber + "\nFarmers Data -> " + new Gson().toJson(manualCallList) + "\nFarmers Count : " + manualCallList.size();
        model.setApiData(data);
        model.setApiName("\nApi Name : v2/farmerRegistrationSubmit\n | " + (TextUtils.isEmpty(s) ? "Api Called" : s));
        String userData = preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation");
        model.setUserData("\nUSER DATA : -> " + userData);
        AppLogFile.INSTANCE.writeLineInFile(new Gson().toJson(model), preference.getString("ecn"));

        HashMap<String, Object> bundle = new HashMap<>();
        bundle.put(FirebaseAnalytics.Param.ITEM_ID, preference.getString("user_id"));
        bundle.put("Api_Name", "v2/farmerRegistrationSubmit");
        bundle.put("user_detail", userData);
        bundle.put("api_data", data);
        if (s.contains("Error")) {
            bundle.put("error_message", s);
        } else {
            bundle.put("message", s);
        }
        Utility.INSTANCE.logEvent("FarmersRegistrationActivity", "add_non_ifc_farmer", bundle);
    }

    private void alertDialog() {
        new AlertDialog.Builder(this).setTitle("Register Farmer").setMessage("Farmer are less than 15, Are you sure you want to submit?")
                .setPositiveButton("Yes", (dialogInterface, i) -> uploadData()).setNegativeButton("No", (dialogInterface, i) -> dialogInterface.dismiss()).show();
    }

    @SuppressLint("NonConstantResourceId")
    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.ivAddMnNumber:
                if (ticketId != null) {
                    if (!ticketId.isEmpty()) {
                        Intent intent = new Intent(getApplicationContext(), AddNumberActivity.class);
                        startActivityForResult(intent, requestCodePlan);
                    } else makeToast("No Active Ticket found!");
                } else makeToast("No Active Ticket found!");
                break;
            case R.id.ivBack:
                onBackPressed();
                break;
            case R.id.btnRbSubmit:
                if (ticketId != null) {
                    if (!ticketId.isEmpty()) {
                        if (manualCallList.size() + autoCallLogList.size() > 15)
                            uploadData();
                        else alertDialog();
                    } else makeToast("No Active Ticket found!");
                } else makeToast("No Active Ticket found!");
                break;
            default:
                break;
        }

    }

    @Override
    public void onUpdateListFromAdapterAction(String type, int position) {
        if (type.equalsIgnoreCase("m")) {
            logEventAddFarmers("Removed item : " + new Gson().toJson(manualCallList.get(position)));
            manualCallList.remove(position);
            manualCallAdapter.notifyDataSetChanged();
        } else {
            callLogAdapter.notifyDataSetChanged();
        }
        logEventAddFarmers("List After Removed item : " + new Gson().toJson(manualCallList));
    }

    @Override
    public void onAsyncComplete() {
        updateManualCall();
    }

    @Override
    public void onFailureFarmers(String message) {
        hidep();
        makeToast(message);
        logEventAddFarmers("Error : " + message);
    }

    private class GetAllTicketListFromDB extends AsyncTask<Void, Void, TicketHistoryModel.Datum> {

        @Override
        protected TicketHistoryModel.Datum doInBackground(Void... voids) {
            List<TicketHistoryModel.Datum> ticketList = appDatabase.ticketHistoryDao().getAllTicketHistory();
            for (int i = 0; i < ticketList.size(); i++) {
                TicketHistoryModel.Datum datum = ticketList.get(i);
                if (datum.getTicketStatus().equalsIgnoreCase("ACTIVE") && datum.getDdId().equals("0")
                        && !datum.getDataSource().equalsIgnoreCase("IFC")) {
                    return datum;
                }
            }
            return null;
        }

        @Override
        protected void onPostExecute(TicketHistoryModel.Datum datum) {
            super.onPostExecute(datum);
            if (datum != null) {
                TextView tvReTicketNoDisplay = findViewById(R.id.tvReTicketNoDisplay);
                TextView tvMeTicketTime = findViewById(R.id.tvMeTicketTime);
                tvReTicketNoDisplay.setText(datum.getTicket());
                tvMeTicketTime.setText(datum.getDatetimeFormat());
                ticketTime = datum.getDatetime();
                ticketId = String.valueOf(datum.gettID());
                ticketNumber = datum.getTicket();
                loadLogs();
                makeToast(getResources().getString(R.string.no_internet_connection));
                new GetRegisteredNumberFromDb().execute();
            } else
                Toast.makeText(getApplicationContext(), "No Active Ticket Found.", Toast.LENGTH_LONG).show();
            hidep();
        }
    }

    private class GetRegisteredNumberFromDb extends AsyncTask<Void, Void, List<FarmerRegistrationBean>> {

        @Override
        protected List<FarmerRegistrationBean> doInBackground(Void... voids) {
            List<FarmerRegistrationBean> numberList = appDatabase.farmerRegistrationDao().getAllNumberList(ticketNumber, 0);
            return numberList;
        }

        @Override
        protected void onPostExecute(List<FarmerRegistrationBean> farmerRegistrationBeans) {
            super.onPostExecute(farmerRegistrationBeans);
            notifyAdapter(farmerRegistrationBeans);
        }
    }

    private class InsertFarmerNumberInDB extends AsyncTask<Void, Void, List<FarmerRegistrationBean>> {

        String mobileNumber;

        InsertFarmerNumberInDB(String mobileNumber) {
            this.mobileNumber = mobileNumber;
        }

        @Override
        protected List<FarmerRegistrationBean> doInBackground(Void... voids) {
            SimpleDateFormat formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH);
            FarmerRegistrationBean farmerRegistrationBean = new FarmerRegistrationBean();
            farmerRegistrationBean.setTicketNumber(ticketNumber);
            farmerRegistrationBean.setTicketId(ticketId);
            farmerRegistrationBean.setStatus(1);
            farmerRegistrationBean.setIsSync(0);
            farmerRegistrationBean.setIsDeleted(0);
            farmerRegistrationBean.setType("m");
            farmerRegistrationBean.setTimeStamp(formatter1.format(new Date()));
            farmerRegistrationBean.setMobileNumber(mobileNumber);
            appDatabase.farmerRegistrationDao().insert(farmerRegistrationBean);
            appDatabase.ticketHistoryDao().isFarmerRegistered(ticketNumber, true);
            List<FarmerRegistrationBean> numberList = appDatabase.farmerRegistrationDao().getAllNumberList(ticketNumber, 0);
            return numberList;
        }

        @Override
        protected void onPostExecute(List<FarmerRegistrationBean> farmerRegistrationBeans) {
            super.onPostExecute(farmerRegistrationBeans);
            notifyAdapter(farmerRegistrationBeans);
        }
    }

    private class updateFarmerRegisteredInDb extends AsyncTask<Void, Void, Void> {

        @Override
        protected Void doInBackground(Void... voids) {
//            TicketHistoryModel.Datum datum = appDatabase.ticketHistoryDao().getOfflineTicket(ticketNumber);
//            int count = datum.getTotalFarmers();
            int total = manualCallList.size();
            appDatabase.ticketHistoryDao().updateTotalFarmer(ticketNumber, total);
//            System.out.println("updateFarmerRegisteredInDb.doInBackground : " + id);
            return null;
        }
    }
}
