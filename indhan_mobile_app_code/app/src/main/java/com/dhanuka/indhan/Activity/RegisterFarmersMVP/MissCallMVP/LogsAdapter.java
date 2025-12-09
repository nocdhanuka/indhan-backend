//package com.dhanuka.indhan.Activity.RegisterFarmersMVP.MissCallMVP;
//
//import android.Manifest;
//import android.annotation.SuppressLint;
//import android.content.Context;
//import android.support.annotation.RequiresPermission;
//import android.view.LayoutInflater;
//import android.view.View;
//import android.view.ViewGroup;
//import android.widget.ArrayAdapter;
//import android.widget.CheckBox;
//import android.widget.CompoundButton;
//
//import com.dhanuka.indhan.Fonts.Medium;
//import com.dhanuka.indhan.Fonts.Regular;
//import com.dhanuka.indhan.R;
//import com.dhanuka.indhan.util.Utility;
//import com.wickerlabs.logmanager.LogObject;
//
//import java.text.SimpleDateFormat;
//import java.util.ArrayList;
//import java.util.Date;
//import java.util.List;
//
///**
// * Created by Shivam on 3/12/2018.
// */
//
//public class LogsAdapter extends ArrayAdapter<LogObject> {
//
//	public static ArrayList<LogObject> unselectedLogValues = new ArrayList<>();
//	public static ArrayList<LogObject> selectedLogValues = new ArrayList<>();
//	SimpleDateFormat simpleDateFormat;
//	String time;
//	List<LogObject> logs;
//	Context context;
//	//CheckBox checkBox;
//	int resource;
//	private ArrayList<String> selectedNumbers;
//	private LogObject log;
//
//	public LogsAdapter(Context context, int resource,
//					   List<LogObject> callLogs,
//					   ArrayList<String> selectedNumbers) {
//
//		super(context, resource, callLogs);
//		this.logs = callLogs;
//		this.context = context;
//		this.resource = resource;
//		this.selectedNumbers = selectedNumbers;
//		unselectedLogValues.clear();
//		selectedLogValues.clear();
//	}
//
//	public ArrayList<LogObject> getSelectedList() {
//		return selectedLogValues;
//	}	@Override
//	public Context getContext() {
//		return context;
//	}
//
//	public ArrayList<LogObject> getUnSelectedList() {
//		return unselectedLogValues;
//	}	@Override
//	public int getCount() {
//		return logs.size();
//	}
//
//	@Override
//	public LogObject getItem(int position) {
//		return logs.get(position);
//	}
//
//	@Override
//	@SuppressLint("ViewHolder")
//	@RequiresPermission(Manifest.permission.READ_CONTACTS)
//	public View getView(final int position, View convertView, ViewGroup parent) {
//		View row = LayoutInflater.from(getContext()).inflate(resource, parent, false);
//		log = getItem(position);
//
//		final CheckBox check_sel = row.findViewById(R.id.cbdelete);
//		check_sel.setVisibility(View.VISIBLE);
//
//		String strLog = log.getNumber() + String.valueOf(log.getDate());
//		if (selectedNumbers.contains(strLog)) {
//			check_sel.setChecked(true);
//			selectedLogValues.add(logs.get(position));
//		} else {
//			unselectedLogValues.add(logs.get(position));
//			check_sel.setChecked(false);
//		}
//
//		Regular phone = (Regular) row.findViewById(R.id.tvRePhoneNumber);
//		Medium date = (Medium) row.findViewById(R.id.tvMeCallTime);
//
//		Date date1 = new Date(log.getDate());
//		phone.setText(log.getContactName());
//
//		simpleDateFormat = new SimpleDateFormat("hh:mm a");
//		time = simpleDateFormat.format(date1);
//		date.setText(time);
//
//		check_sel.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
//			@Override
//			public void onCheckedChanged(CompoundButton compoundButton, boolean b) {
//				if (b) {
//                   /* selectedLogValues.add(logs.get(position));
//                    unselectedLogValues.remove(logs.get(position));*/
//					Utility.saveSelectedNumber(context, logs.get(position));
//				} else {
//                   /* selectedLogValues.remove(logs.get(position));
//                    unselectedLogValues.add(logs.get(position));*/
//					Utility.removeSelectedNumber(context, logs.get(position));
//				}
//
//				//  Log.e("values",""+selectedLogValues.toString() +"and"+unselectedLogValues.toString());
//			}
//		});
//
//		return row;
//	}
//
//
//
//
//
//}
