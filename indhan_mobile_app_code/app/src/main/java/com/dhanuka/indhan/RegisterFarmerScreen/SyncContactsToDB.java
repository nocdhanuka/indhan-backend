package com.dhanuka.indhan.RegisterFarmerScreen;

import android.content.Context;
import android.os.AsyncTask;

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Model.RegisterFarmerContactModel;

import java.util.List;

/**
 * Created by John on 4/9/2018.
 */
public class SyncContactsToDB extends AsyncTask<Void, Void, Void> {
    private final String mTicketId;
    Context ctx;
    AsyncPost listener;
    List<RegisterFarmerContactModel> callLoglist;
    DatabaseHandler db;
    SubmitFarmersContracts.ISubmitFarmersView iview;
    private String ticketTime = "";

    public SyncContactsToDB(Context ctx, List<RegisterFarmerContactModel> callLoglist, String ticketTime, String ticketId) {
        this.ctx = ctx;
        this.listener = (AsyncPost) ctx;
        this.callLoglist = callLoglist;
        this.ticketTime = ticketTime;
        db = new DatabaseHandler(ctx);
        mTicketId = ticketId;
        iview = (SubmitFarmersContracts.ISubmitFarmersView) ctx;
    }

    @Override
    protected Void doInBackground(Void... voids) {
	/*	try {
			LogsManager logsManager = new LogsManager(ctx);
			List<LogObject> callLogs = logsManager.getLogs(LogsManager.ALL_CALLS);
			SimpleDateFormat formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			Date date1 = null;
			Date date2 = null;
			for (int i = 0; i < callLogs.size(); i++) {
				LogObject autoContact = callLogs.get(i);
				try {
					String dateFormat = TimeStamp.getDate(autoContact.getDate());
					date1 = formatter1.parse(dateFormat);
					date2 = formatter1.parse(ticketTime);
				} catch (ParseException e) {
					e.printStackTrace();
				}
				if (date1.after(date2)) {
					Log.e("Vinod", "Vinod contact name | " + autoContact.getContactName());
					if (!checkNumberInList(autoContact.getNumber())) {
						RegisterFarmerContactModel model = new RegisterFarmerContactModel();
						model.setStatus(1);
						model.setIsSync(0);
						model.setIsDeleted(0);
						model.setType("a");
						model.setTicketNumber(mTicketId);
						model.setLast_modified(formatter1.format(new Date()));
						String number=autoContact.getNumber();
						String numbers="";
						if(number.length()>=10)
							numbers= number.substring(number.length() - 10, number.length());
						else numbers="";
						model.setPhoneNumber(numbers);
						model.setTimestamp(TimeStamp.getDate(autoContact.getDate()));
						if (!autoContact.getNumber().contains(autoContact.getContactName())) {
							model.setContactName(autoContact.getContactName());
						} else {
							model.setContactName("");
						}
						if(numbers.length()>=10) {
							if (!db.checkinDb(model)) {
								callLoglist.add(model);
								db.addRegisterFarmerContact(model);
							}
						}
					}
				}
			}
		} catch (Exception e) {
			//  Log.e("exception submit", "" + e);
			return null;
		}*/
        return null;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        iview.showProgress();
    }

    @Override
    protected void onPostExecute(Void aVoid) {
        super.onPostExecute(aVoid);
        listener.onAsyncComplete();
    }

    private boolean checkNumberInList(String number) {
        if (number.length() < 10) {
            return true;
        }
        if (callLoglist.size() > 0) {
            for (RegisterFarmerContactModel call : callLoglist) {
                if (call.getPhoneNumber().contains(number)) {
                    return true;
                }
            }
        }
        return false;
    }

    public interface AsyncPost {
        void onAsyncComplete();
    }
}
