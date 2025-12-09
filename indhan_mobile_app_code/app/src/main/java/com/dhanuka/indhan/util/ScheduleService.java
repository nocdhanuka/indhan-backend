package com.dhanuka.indhan.util;

import android.app.Service;
import android.content.Intent;
import android.os.Handler;
import android.os.IBinder;
import android.widget.Toast;

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Model.CommonModel;
import com.dhanuka.indhan.Preference.Preference;

/**
 * Created by Jitender Kumar on 4/4/2018.
 */

public class ScheduleService extends Service implements SubmitFarmersContracts.ISubmitFarmersView {

    public static final int mInterval = 1000 * 60 * 10;  //interval between two services(Here Service run every 5 seconds)
    /*    private ArrayList<LogObject> stateRecordResponseModel = new ArrayList<>();
        private ArrayList<LogObject> numberExistRecordResponseModel = new ArrayList<>();*/
    DatabaseHandler db;
    private SubmitFarmersContracts.SubmitFarmersPresenter submitFarmersPresenter;
    private String Token = "", ticketTime = "";
    private String ticketId;
    private Preference preference;
    private Handler mHandler;
    Runnable mStatusChecker = new Runnable() {
        @Override
        public void run() {
            try {
                displayData();
            } finally {
                mHandler.postDelayed(mStatusChecker, mInterval);
            }
        }
    };

    @Override
    public IBinder onBind(Intent intent) {
        throw new UnsupportedOperationException("Not yet implemented");
    }

    @Override
    public void onCreate() {
        mHandler = new Handler();
        startRepeatingTask();
          /*  new Handler().postDelayed(new Runnable() {
                @Override
                public void run() {
                    Log.e("called 10 inetdval","called");
                    displayData();
                }
            }, notify);*/


    }

    void startRepeatingTask() {
        mStatusChecker.run();
    }

    void stopRepeatingTask() {
        mHandler.removeCallbacks(mStatusChecker);
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        return super.onStartCommand(intent, flags, startId);
    }

    void displayData() {
    /*    db = new DatabaseHandler(this);
        preference = Preference.getInstance(this);
        Token = Preference.getInstance(this).getString("token");
        ticketId = Preference.getInstance(this).getString("ticketId");
        ticketTime = Preference.getInstance(this).getString("ticketTime");

        if (ticketId != null && !ticketId.isEmpty()) {
            loadLogs();
            if (numberExistRecordResponseModel.size() != 0) {
                //     Toast.makeText(this, "Syncing", Toast.LENGTH_SHORT).show();
                submitFarmersPresenter = new SubmitFarmersPresenterImpl(this);
                if (numberExistRecordResponseModel != null && numberExistRecordResponseModel.size() > 0) {
                    JSONArray jsonArrayMissed = new JSONArray();
                    for (int i = 0; i < numberExistRecordResponseModel.size(); i++) {
                        JSONObject mJsonObject = new JSONObject();
                        try {
                            String dateConvert = TimeStamp.getDate(numberExistRecordResponseModel.get(i).getDate());
                            mJsonObject.put("farmer_number", numberExistRecordResponseModel.get(i).getNumber());
                            mJsonObject.put("datetime", dateConvert);
                            jsonArrayMissed.put(mJsonObject);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                    HashMap<String, Object> query = new HashMap<>();
                    query.put("api_token", Token);
                    query.put("tId", ticketId);
                    query.put("manual_data", "");
                    query.put("missed_data", jsonArrayMissed.toString());
                    query.put("flag_missed", "");
                    query.put("unflag_missed", "");
                    if (CheckConnectionStatus.isInternetAvailable(ScheduleService.this)) {
                        submitFarmersPresenter.onSubmitClick(query);
                    }
                }
            }
        }*/

    }

    private void loadLogs() {
       /* stateRecordResponseModel.clear();
        numberExistRecordResponseModel.clear();
        LogsManager logsManager = new LogsManager(this);
        List<LogObject> callLogs = logsManager.getLogs(LogsManager.ALL_CALLS);
        SimpleDateFormat formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date date1 = null;
        Date date2 = null;
        for (int i = 0; i < callLogs.size(); i++) {
            try {
                String dateFormat = TimeStamp.getDate(callLogs.get(i).getDate());
                date1 = formatter1.parse(dateFormat);
                date2 = formatter1.parse(ticketTime);
            } catch (ParseException e) {
                e.printStackTrace();
            }
            if (date1.after(date2)) {
                stateRecordResponseModel.add(callLogs.get(i));
            }
            for (int k = 0; k < stateRecordResponseModel.size(); k++) {
                if (stateRecordResponseModel.get(k).getContactName().matches("^[a-zA-Z]+(\\s[a-zA-Z]+)?$") == false)
                    numberExistRecordResponseModel.add(stateRecordResponseModel.get(k));
            }
        }*/
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void showProgress() {

    }

    @Override
    public void hideProgress() {

    }

    @Override
    public void setItems(CommonModel items) {
        if (items.getStatus_code() == 200) {
            // Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        } else if (items.getStatus_code() == 404) {
            preference.putString("ticketId", "");
            db.deleteAll();
            Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        } else {
            Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }


    @Override
    public void onFailureFarmers(String message) {
        Toast.makeText(getApplicationContext(), message, Toast.LENGTH_SHORT).show();
    }
}