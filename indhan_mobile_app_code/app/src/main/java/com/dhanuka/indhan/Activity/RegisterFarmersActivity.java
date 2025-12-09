//package com.dhanuka.indhan.Activity;
//
//import android.Manifest;
//import android.annotation.TargetApi;
//import android.app.AlertDialog;
//import android.content.DialogInterface;
//import android.content.Intent;
//import android.content.pm.PackageManager;
//import android.os.Build;
//import android.os.Bundle;
//import android.support.annotation.NonNull;
//import android.support.v4.content.ContextCompat;
//import android.support.v4.content.PermissionChecker;
//import android.support.v7.widget.DefaultItemAnimator;
//import androidx.recyclerview.widget.LinearLayoutManager;
//import androidx.recyclerview.widget.RecyclerView;
//import android.util.Log;
//import android.view.View;
//import android.widget.LinearLayout;
//import android.widget.ListView;
//
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.ManuallyNumberClickListener;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.MissCallMVP.LogsAdapter;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.RegisterFarmersContracts;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.RegisterFarmersPresenterImpl;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersContracts;
//import com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP.SubmitFarmersPresenterImpl;
//import com.dhanuka.indhan.DataBase.Contact;
//import com.dhanuka.indhan.DataBase.DatabaseHandler;
//import com.dhanuka.indhan.Fonts.Medium;
//import com.dhanuka.indhan.Fonts.Regular;
//import com.dhanuka.indhan.Fonts.RegularButton;
//import com.dhanuka.indhan.Model.AddNumberModel;
//import com.dhanuka.indhan.Model.CommonModel;
//import com.dhanuka.indhan.Model.SelectedNumbers;
//import com.dhanuka.indhan.Preference.Preference;
//import com.dhanuka.indhan.R;
//import com.dhanuka.indhan.util.CheckConnectionStatus;
//import com.dhanuka.indhan.util.ListViewExpand;
//import com.dhanuka.indhan.util.SelectedNumberModel;
//import com.dhanuka.indhan.util.TimeStamp;
//import com.wickerlabs.logmanager.LogObject;
//import com.wickerlabs.logmanager.LogsManager;
//
//import org.json.JSONArray;
//import org.json.JSONException;
//import org.json.JSONObject;
//
//import java.io.Serializable;
//import java.text.ParseException;
//import java.text.SimpleDateFormat;
//import java.util.ArrayList;
//import java.util.Collection;
//import java.util.Date;
//import java.util.LinkedHashMap;
//import java.util.List;
//
///**
// * Created by Shivam on 3/7/2018.
// */
//@TargetApi(Build.VERSION_CODES.JELLY_BEAN)
//public class RegisterFarmersActivity extends BaseActivity implements RegisterFarmersContracts.IRegisterFarmersView,
//																	 SubmitFarmersContracts.ISubmitFarmersView, ManuallyNumberClickListener {
//
//	private static final int READ_LOGS = 725;
//	DatabaseHandler db;
//	private ArrayList<AddNumberModel> addNumberModels = new ArrayList<>();
//	private ArrayList<AddNumberModel> checkNumberModels = new ArrayList<>();
//	private RegisterFarmersContracts.RegisterFarmersPresenter mRegisterFarmersPresenter;
//	private String Token = "", ticketTime = "";
//	private Regular tvReTicketNoDisplay;
//	private Medium tvMeTicketTime, tvMeManualHint, tvMeMissedHint;
//	private LinearLayout ivAddMnNumber, ivBack;
//	private int requestCodePlan = 101;
//	private Serializable plansRecordResponseModel = new ArrayList<>();
//	private RecyclerView mRecyclerView;
//	private RegularButton btnRbSubmit;
//	private SubmitFarmersContracts.SubmitFarmersPresenter submitFarmersPresenter;
//	private String ticketId;
//	private ArrayList<LogObject> stateRecordResponseModel = new ArrayList<>();
//	private ArrayList<LogObject> numberExistRecordResponseModel = new ArrayList<>();
//	private ArrayList<SelectedNumbers> melectedNumber = new ArrayList<>();
//	private ListView logList;
//	private Runnable logsRunnable;
//	private Preference preference;
//	private String[] requiredPermissions = {Manifest.permission.READ_CALL_LOG, Manifest.permission.READ_CONTACTS};
//	private List<Contact> contacts;
//	private List<Contact> updatedContacts;
//	private LogsAdapter logsAdapter;
//	private SelectedNumberModel selectedNumberModel;
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.activity_register_farmers);
//		db = new DatabaseHandler(this);
//		contacts = db.getAllContacts();
//		updatedContacts = db.getAllStatusContacts();
////        db.deleteAll();
//		preference = Preference.getInstance(this);
//		Token = Preference.getInstance(this).getString("token");
//		mRegisterFarmersPresenter = new RegisterFarmersPresenterImpl(this);
//
//		ivAddMnNumber = findViewById(R.id.ivAddMnNumber);
//		btnRbSubmit = findViewById(R.id.btnRbSubmit);
//		ivBack = findViewById(R.id.ivBack);
//
//		ivAddMnNumber.setOnClickListener(this);
//		ivBack.setOnClickListener(this);
//		btnRbSubmit.setOnClickListener(this);
//
//		tvReTicketNoDisplay = findViewById(R.id.tvReTicketNoDisplay);
//		tvMeTicketTime = findViewById(R.id.tvMeTicketTime);
//		tvMeManualHint = findViewById(R.id.tvMeManualHint);
//		tvMeMissedHint = findViewById(R.id.tvMeMissedHint);
//
//		if (CheckConnectionStatus.isInternetAvailable(RegisterFarmersActivity.this)) {
//			mRegisterFarmersPresenter.onSubmitClick(Token);
//		} else {
//			makeToast(getResources()
//							  .getString(R.string.no_internet_connection));
//		}
//
//		mRecyclerView = (RecyclerView) findViewById(R.id.rvManualAdded);
//		LinearLayoutManager mLayoutManager = new LinearLayoutManager(this);
//		mRecyclerView.setHasFixedSize(true);
//		mRecyclerView.setLayoutManager(mLayoutManager);
//		mRecyclerView.setItemAnimator(new DefaultItemAnimator());
//		submitFarmersPresenter = new SubmitFarmersPresenterImpl(this);
//		logList = (ListView) findViewById(R.id.rvMissCall);
//
//		logsRunnable = new Runnable() {
//			@Override
//			public void run() {
////                loadLogs();
//			}
//		};
//		// Checking for permissions
//		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
//			checkPermissionToExecute(requiredPermissions, READ_LOGS, logsRunnable);
//		} else {
//			logsRunnable.run();
//		}
//	}
//
//
//	@Override
//	public void onResume() {
//		super.onResume();
//
//	}
//
//	// A method to check_sel if a permission is granted then execute tasks depending on that particular permission
//	@TargetApi(Build.VERSION_CODES.M)
//	private void checkPermissionToExecute(String permissions[], int requestCode, Runnable runnable) {
//		boolean logs = ContextCompat.checkSelfPermission(this, permissions[0]) != PackageManager.PERMISSION_GRANTED;
//		boolean contacts = ContextCompat.checkSelfPermission(this, permissions[1]) != PackageManager.PERMISSION_GRANTED;
//		if (logs || contacts) {
//			requestPermissions(permissions, requestCode);
//		} else {
//			runnable.run();
//		}
//	}
//
//	@Override
//	public void onClick(View v) {
//		switch (v.getId()) {
//			case R.id.ivAddMnNumber:
//				Intent intent = new Intent(
//						getApplicationContext(),
//						AddNumberActivity.class);
//				startActivityForResult(intent, requestCodePlan);
//				break;
//			case R.id.ivBack:
//				onBackPressed();
//				break;
//			case R.id.btnRbSubmit:
//				contacts.clear();
//				contacts.addAll(db.getAllContacts());
//				if (ticketId != null) {
//					if (numberExistRecordResponseModel.size() != 0 || updatedContacts.size() != 0) {
//
//						// Manual Contacts Array
//						JSONArray jsonArray = new JSONArray();
//						for (int i = 0; i < contacts.size(); i++) {
//							JSONObject mJsonObject = new JSONObject();
//							try {
//								mJsonObject.put("farmer_number",
//												contacts.get(i).getPhoneNumber());
//								mJsonObject.put("datetime",
//												contacts.get(i).getName());
//								mJsonObject.put("status",
//												contacts.get(i).getStatus());
//
//								jsonArray.put(mJsonObject);
//							} catch (JSONException e) {
//								e.printStackTrace();
//							}
//						}
//
//						// Missed Contacts Array
//						JSONArray jsonArrayMissed = new JSONArray();
//						for (int i = 0; i < numberExistRecordResponseModel.size(); i++) {
//							JSONObject mJsonObject = new JSONObject();
//							try {
//								String dateConvert = TimeStamp.getDate(numberExistRecordResponseModel.get(i).getDate());
//								mJsonObject.put("farmer_number", numberExistRecordResponseModel.get(i).getNumber());
//								mJsonObject.put("datetime", dateConvert);
//								jsonArrayMissed.put(mJsonObject);
//							} catch (JSONException e) {
//								e.printStackTrace();
//							}
//						}
//
//						ArrayList<LogObject> flagObject = getSelectedList();
//
//						// Flag Contacts Array
//						JSONArray jsonArrayFlagged = new JSONArray();
//						for (int i = 0; i < flagObject.size(); i++) {
//							JSONObject mJsonObject = new JSONObject();
//							try {
//								String dateConvert = TimeStamp.getDate(flagObject.get(i).getDate());
//								mJsonObject.put("farmer_number", flagObject.get(i).getNumber());
//								mJsonObject.put("datetime", dateConvert);
//								jsonArrayFlagged.put(mJsonObject);
//							} catch (JSONException e) {
//								e.printStackTrace();
//							}
//						}
//
//						ArrayList<LogObject> unFlagObject = getUnSelectedList();
//
//						// Unflag Contacts Array
//						JSONArray jsonArrayUnFlagged = new JSONArray();
//						for (int i = 0; i < unFlagObject.size(); i++) {
//							JSONObject mJsonObject = new JSONObject();
//							try {
//								String dateConvert = TimeStamp.getDate(unFlagObject.get(i).getDate());
//								mJsonObject.put("farmer_number", unFlagObject.get(i).getNumber());
//								mJsonObject.put("datetime", dateConvert);
//								jsonArrayUnFlagged.put(mJsonObject);
//							} catch (JSONException e) {
//								e.printStackTrace();
//							}
//						}
//
//						LinkedHashMap<String,Object> query = new LinkedHashMap<>();
//
//						query.put("api_token", Token);
//						query.put("tId", ticketId);
//
//						if (contacts.size() == 0) {
//							query.put("manual_data", "");
//						} else {
//							query.put("manual_data", jsonArray.toString());
//						}
//
//						if (numberExistRecordResponseModel.size() == 0) {
//							query.put("missed_data", "");
//						} else {
//							query.put("missed_data", jsonArrayMissed.toString());
//						}
//
//						if (flagObject.size() == 0) {
//							query.put("flag_missed", "");
//						} else {
//							query.put("flag_missed", jsonArrayFlagged.toString());
//						}
//
//						if (unFlagObject.size() == 0) {
//							query.put("unflag_missed", "");
//						} else {
//							query.put("unflag_missed", jsonArrayUnFlagged.toString());
//						}
//
//						Log.e("end quesry", "" + query);
//
//						if (CheckConnectionStatus.isInternetAvailable
//								(RegisterFarmersActivity.this)) {
//							submitFarmersPresenter.onSubmitClick(query);
//						} else {
//							makeToast(getResources().getString(R.string.no_internet_connection));
//						}
//					} else {
//						makeToast("No number found!");
//					}
//
//				}
//				break;
//			default:
//				break;
//		}
//	}
//
//	private ArrayList<LogObject> getSelectedList() {
//		if (numberExistRecordResponseModel == null)
//			return new ArrayList<>();
//		ArrayList<LogObject> selectedList = null;
//		if (selectedNumberModel == null)
//			selectedNumberModel = new SelectedNumberModel();
//
//		try {
//			selectedNumberModel = Preference.getPreferenceSelected
//					(RegisterFarmersActivity.this, "selectednum");
//
//			selectedList = new ArrayList<>();
//			for (LogObject object : numberExistRecordResponseModel) {
//				String strLog = object.getNumber() + String.valueOf(object.getDate());
//				if (selectedNumberModel.getSelectedNumbers().contains(strLog)) {
//					selectedList.add(object);
//				}
//			}
//		} catch (Exception e) {
//
//		}
//		return selectedList;
//	}
//
//	private ArrayList<LogObject> getUnSelectedList() {
//		if (numberExistRecordResponseModel == null)
//			return new ArrayList<>();
//       /* if (selectedNumberModel==null)
//        selectedNumberModel=new SelectedNumberModel();*/
//		selectedNumberModel = Preference.getPreferenceSelected
//				(RegisterFarmersActivity.this, "selectednum");
//
//		ArrayList<LogObject> unSelectedList = new ArrayList<>();
//		try {
//			for (LogObject object : numberExistRecordResponseModel) {
//				String strLog = object.getNumber() + String.valueOf(object.getDate());
//				if (selectedNumberModel != null) {
//					if (!selectedNumberModel.getSelectedNumbers().contains(strLog)) {
//						unSelectedList.add(object);
//					}
//				} else {
//					unSelectedList.add(object);
//				}
//
//			}
//		} catch (Exception e) {
//			Log.e("exception unselected", "" + e);
//		}
//
//		return unSelectedList;
//	}
//
//	@Override
//	public void setItems(CommonModel items) {
//		if (items.getStatus_code() == 200) {
//			makeToast(items.getMessage());
//			for (int i = 0; i < items.getData().size(); i++) {
//				Log.d("MissedData", items.getData().get(i).getNumber());
//			}
//			launchIntent(HomeActivity.class, true);
//		} else if (items.getStatus_code() == 404) {
//			preference.putString("ticketId", "");
//			db.deleteAll();
//			makeToast(items.getMessage());
//			launchIntent(HomeActivity.class, true);
//		} else {
//			makeToast(items.getMessage());
//		}
//	}
//
//	@Override
//	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
//
//		if (requestCode == requestCodePlan && resultCode == 101) {
//			if (data.getExtras() != null && data.getSerializableExtra("UNIQUE_NUMBERS") != null) {
//				mRecyclerView.setVisibility(View.VISIBLE);
//				plansRecordResponseModel = data.getExtras().getSerializable("UNIQUE_NUMBERS");
//				checkNumberModels.addAll((Collection<? extends AddNumberModel>) plansRecordResponseModel);
//				tvMeManualHint.setVisibility(View.GONE);
//
//				if (updatedContacts.size() > 0) {
//					for (int i = 0; i < checkNumberModels.size(); i++) {
//						if (!checkNumberExits("" + checkNumberModels.get(i).getNumber(), updatedContacts))
//							db.addContact(new Contact(checkNumberModels.get(i).getServerTime(),
//													  checkNumberModels.get(i).getNumber(), checkNumberModels.get(i).getStatus()));
//					}
//				} else {
//					for (int i = 0; i < checkNumberModels.size(); i++) {
//						db.addContact(new Contact(checkNumberModels.get(i).getServerTime(), checkNumberModels.get(i).getNumber(), checkNumberModels.get(i).getStatus()));
//					}
//				}
//
//				updatedContacts.clear();
//				checkNumberModels.clear();
//				updatedContacts.addAll(db.getAllStatusContacts());
//			}
//		}
//		super.onActivityResult(requestCode, resultCode, data);
//	}	@Override
//	public void hideProgress() {
//		dismissProgress();
//	}
//
//	private boolean checkNumberExits(String number, List<Contact> contacts) {
//		boolean isExist = false;
//		for (int i = 0; i < contacts.size(); i++) {
//			if (contacts.get(i).getPhoneNumber().equalsIgnoreCase(number)) {
//				isExist = true;
//				return isExist;
//			}
//		}
//		return isExist;
//	}
//
//	@Override
//	@TargetApi(Build.VERSION_CODES.M)
//	public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
//		super.onRequestPermissionsResult(requestCode, permissions, grantResults);
//
//		if (requestCode == READ_LOGS && permissions[0].equals(Manifest.permission.READ_CALL_LOG) && permissions[1].equals(Manifest.permission.READ_CONTACTS)) {
//			if (grantResults[0] == PermissionChecker.PERMISSION_GRANTED && grantResults[1] == PermissionChecker.PERMISSION_GRANTED) {
//				logsRunnable.run();
//			} else {
//				new AlertDialog.Builder(RegisterFarmersActivity.this)
//						.setMessage("The app needs these permissions to work, Exit?")
//						.setTitle("Permission Denied")
//						.setCancelable(false)
//						.setPositiveButton("Retry", new DialogInterface.OnClickListener() {
//							@Override
//							public void onClick(DialogInterface dialog, int which) {
//								dialog.dismiss();
//								checkPermissionToExecute(requiredPermissions, READ_LOGS, logsRunnable);
//							}
//						})
//						.setNegativeButton("Exit App", new DialogInterface.OnClickListener() {
//							@Override
//							public void onClick(DialogInterface dialog, int which) {
//								dialog.dismiss();
//								finish();
//							}
//						})
//						.show();
//			}
//		}
//	}
//
//	@Override
//	public void onClick(View view, Contact contact) {
//
//	}
//
//	@Override
//	public void setItems(RegisterFarmersBean items) {
//		if (items.getStatus_code() == 200) {
//			tvReTicketNoDisplay.setText(items.getData().get(0).getTicket_no());
//			tvMeTicketTime.setText(items.getData().get(0).getDatetime_format());
//			ticketTime = items.getData().get(0).getDatetime();
//			ticketId = String.valueOf(items.getData().get(0).gettID());
//			loadLogs();
//		} else {
//			makeToast(items.getMessage());
//		}
//	}
//
//
//
//
//	private void loadLogs() {
//		try {
//
//			SelectedNumbers selectedNumbers = new SelectedNumbers();
//
//			stateRecordResponseModel.clear();
//			numberExistRecordResponseModel.clear();
//			melectedNumber.clear();
//			LogsManager logsManager = new LogsManager(this);
//			List<LogObject> callLogs = logsManager.getLogs(LogsManager.ALL_CALLS);
//			SimpleDateFormat formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
//			Date date1 = null;
//			Date date2 = null;
//			for (int i = 0; i < callLogs.size(); i++) {
//				try {
//					String dateFormat = TimeStamp.getDate(callLogs.get(i).getDate());
//					date1 = formatter1.parse(dateFormat);
//					date2 = formatter1.parse(ticketTime);
//				} catch (ParseException e) {
//					e.printStackTrace();
//				}
//				if (date1.after(date2)) {
//					stateRecordResponseModel.add(callLogs.get(i));
//				}
//			}
//
//			for (int k = 0; k < stateRecordResponseModel.size(); k++) {
//				// if(contactExists(this, stateRecordResponseModel.get(k).getContactName()))
//				if (stateRecordResponseModel.get(k).getContactName()
//						.matches("^[a-zA-Z]+(\\s[a-zA-Z]+)?$") == false)
//					//melectedNumber.add(stateRecordResponseModel.get(k).getContactName());
//
//					selectedNumbers.setNumber(stateRecordResponseModel.get(k).getContactName());
//				selectedNumbers.setDatetie(stateRecordResponseModel.get(k).getDate());
//				melectedNumber.add(selectedNumbers);
//
//				numberExistRecordResponseModel.add(stateRecordResponseModel.get(k));
//			}
//
//			if (numberExistRecordResponseModel.size() > 0) {
//				logList.setVisibility(View.VISIBLE);
//				tvMeMissedHint.setVisibility(View.GONE);
//			} else {
//				logList.setVisibility(View.GONE);
//				tvMeMissedHint.setVisibility(View.VISIBLE);
//			}
//
//			selectedNumberModel = Preference.getPreferenceSelected
//					(RegisterFarmersActivity.this, "selectednum");
//
//			ArrayList<String> selectedNum = new ArrayList<>();
//			if (selectedNumberModel != null)
//				selectedNum.addAll(selectedNumberModel.getSelectedNumbers());
//
//			logsAdapter = new LogsAdapter(this, R.layout.number_time_row,
//										  numberExistRecordResponseModel, selectedNum);
//
//			logList.setAdapter(logsAdapter);
//			ListViewExpand.setListViewHeightBasedOnItems(logList);
//			hideProgress();
//		} catch (Exception e) {
//			Log.e("exception submit", "" + e);
//			hideProgress();
//		}
//
//	}
//
//
//}
