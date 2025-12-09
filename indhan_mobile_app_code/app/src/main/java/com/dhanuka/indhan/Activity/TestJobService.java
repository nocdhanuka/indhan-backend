/*
package com.dhanuka.indhan.Activity;

import android.app.job.JobParameters;
import android.app.job.JobService;
import android.widget.Toast;

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersPresenterImpl;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Model.CommonModel;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.util.CheckConnectionStatus;
import com.dhanuka.indhan.util.TimeStamp;
import com.dhanuka.indhan.util.Utility;
import com.wickerlabs.logmanager.LogObject;
import com.wickerlabs.logmanager.LogsManager;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

*/
/**
 * Created by Shivam on 3/13/2018.
 *//*


public class TestJobService extends JobService implements SubmitFarmersContracts.ISubmitFarmersView {
    private SubmitFarmersContracts.SubmitFarmersPresenter submitFarmersPresenter;
    private String Token = "", ticketTime = "";
    private String ticketId;
    private Preference preference;
*/
/*    private ArrayList<LogObject> stateRecordResponseModel = new ArrayList<>();
    private ArrayList<LogObject> numberExistRecordResponseModel = new ArrayList<>();*//*

    DatabaseHandler db;

    @Override
    public boolean onStartJob(JobParameters params) {
        db = new DatabaseHandler(this);
        preference = Preference.getInstance(this);
        Token = Preference.getInstance(this).getString("token");
        ticketId = Preference.getInstance(this).getString("ticketId");
        ticketTime = Preference.getInstance(this).getString("ticketTime");

        if (ticketId != null && !ticketId.isEmpty()) {
            loadLogs();
            if (numberExistRecordResponseModel.size() != 0) {
//                Toast.makeText(this, "Syncing", Toast.LENGTH_SHORT).show();
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
//                    if (CheckConnectionStatus.isInternetAvailable(TestJobService.this)) {
//                        submitFarmersPresenter.onSubmitClick(query);
//                    }
                }
            }
        }
        Utility.scheduleJob(getApplicationContext()); // reschedule the job
        return true;
    }

    @Override
    public boolean onStopJob(JobParameters params) {
        return true;
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
//            Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        } else if (items.getStatus_code() == 404) {
            preference.putString("ticketId", "");
            db.deleteAll();
        } else {
            Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }

    private void loadLogs() {
   */
/*     stateRecordResponseModel.clear();
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
        }*//*

    }

    @Override
    public void onFailure(String message) {

    }
}
*/
