package com.dhanuka.indhan.Activity;

import android.app.job.JobParameters;
import android.app.job.JobService;
import android.os.AsyncTask;
import android.os.Build;
import androidx.annotation.RequiresApi;
import android.util.Log;
import android.widget.Toast;

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersPresenterImpl;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Model.CommonModel;
import com.dhanuka.indhan.Model.RegisterFarmerContactModel;
import com.dhanuka.indhan.Preference.Preference;
import com.dhanuka.indhan.RegisterFarmerScreen.SyncContactsToDB;
import com.dhanuka.indhan.util.CheckConnectionStatus;
import com.dhanuka.indhan.util.Utility;

import java.util.ArrayList;
import java.util.List;


/**
 * Created by Shivam on 3/13/2018.
 */

@RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
public class ScheduledJobService extends JobService implements SubmitFarmersContracts.ISubmitFarmersView, SyncContactsToDB.AsyncPost {
    DatabaseHandler db;
    List<RegisterFarmerContactModel> updateModelListPostApi = new ArrayList<>();
    SyncContactsToDB asynctask;
    private SubmitFarmersContracts.SubmitFarmersPresenter submitFarmersPresenter;
    private String Token = "", ticketTime = "";
    private String ticketId;
    private Preference preference;

    @Override
    public boolean onStartJob(JobParameters params) {
        db = new DatabaseHandler(this);
        preference = Preference.getInstance(this);
        Token = Preference.getInstance(this).getString("token");
        ticketId = Preference.getInstance(this).getString("ticketId");
        ticketTime = Preference.getInstance(this).getString("ticketTime");

        if (ticketId != null && !ticketId.isEmpty()) {
            loadLogs();

        }
        Utility.scheduleJob(getApplicationContext()); // reschedule the job
        return true;
    }

    @Override
    public boolean onStopJob(JobParameters params) {
        return true;
    }

    private void loadLogs() {
        List<RegisterFarmerContactModel> test = db.getAutomaticCallData(Utility.getDatefromFormat(ticketTime));
        if (test != null && test.size() > 0) {
            if (asynctask == null || asynctask.getStatus() != AsyncTask.Status.RUNNING) {
                asynctask = new SyncContactsToDB(this, test, ticketTime, ticketId);
                asynctask.execute();
            }
        }
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
            if (updateModelListPostApi.size() > 0) {
                for (RegisterFarmerContactModel model : updateModelListPostApi) {
                    Log.e("updated john", " updated in back service " + "no.: " + model.getPhoneNumber() + " | isdel : " + model.getIsDeleted());

                    db.updateRegisterContact(model);
                }
                updateModelListPostApi.clear();
            }
        } else if (items.getStatus_code() == 404) {
            preference.putString("ticketId", "");
            db.deleteAll();
        } else {
            Toast.makeText(getApplicationContext(), items.getMessage(), Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onAsyncComplete() {

        if (ticketId != null && !ticketId.isEmpty()) {
            updateModelListPostApi.clear();
            ArrayList<RegisterFarmerContactModel> model = (ArrayList<RegisterFarmerContactModel>) db.getUnsyncedCallLogData(Utility.getDatefromFormat(ticketTime));
            if (model.size() != 0) {
                submitFarmersPresenter = new SubmitFarmersPresenterImpl(this);
                if (model != null && model.size() > 0) {
					
					/*JSONArray jsonArrayMissed = new JSONArray();
					
					for (int i = 0; i < model.size(); i++) {
						Log.e("updated john", " background service " + "no.: " + model.get(i).getPhoneNumber() + " | isdel : " + model.get(i).getIsDeleted());
						
						JSONObject mJsonObject = new JSONObject();
						try {
							mJsonObject.put("farmer_number", model.get(i).getPhoneNumber());
							mJsonObject.put("datetime", model.get(i).getTimestamp());
							jsonArrayMissed.put(mJsonObject);
							model.get(i).setIsSync(1);
							updateModelListPostApi.add(model.get(i));
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}
					HashMap<String,Object> query = new HashMap<>();
					query.put("api_token", Token);
					query.put("tId", ticketId);
					query.put("manual_data", "");
					query.put("missed_data", jsonArrayMissed.toString());
					query.put("flag_missed", "");
					query.put("unflag_missed", "");*/
                    if (Token != null && !Token.isEmpty()) {
                        if (CheckConnectionStatus.isInternetAvailable(ScheduledJobService.this)) {
                            submitFarmersPresenter.onSubmitClickOptimized(model, Token);
                        }
                    }
                }
            }
        }

    }

    @Override
    public void onFailureFarmers(String message) {

    }
}
