package com.dhanuka.indhan.util;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.os.Bundle;
import android.widget.DatePicker;

import androidx.fragment.app.DialogFragment;
import androidx.fragment.app.FragmentManager;

import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class SelectDateFragment extends DialogFragment implements DatePickerDialog.OnDateSetListener {
    private static final String ARGS_KEY = "key";
    private int value;
    private myDialogListener mListener;

    public static DialogFragment newInstance(int key, int requestCode) {
        DialogFragment frag = new SelectDateFragment();
        Bundle args = new Bundle();
        args.putInt(ARGS_KEY, key);
        frag.setArguments(args);
        return frag;
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
        if (getTargetFragment() == null) {
            try {
                mListener = (myDialogListener) activity;
            } catch (ClassCastException e) {
                throw new ClassCastException("Calling activity must implement DialogClickListener interface");
            }
        }
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getTargetFragment() != null) {
            try {
                mListener = (myDialogListener) getTargetFragment();
            } catch (ClassCastException e) {
                throw new ClassCastException("Calling fragment must implement DialogClickListener interface");
            }
        }
    }

    @Override
    public void show(FragmentManager manager, String tag) {
        super.show(manager, tag);

    }

    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        value = getArguments().getInt(ARGS_KEY);
        final Calendar calendar = Calendar.getInstance(Locale.ENGLISH);
        int yy = calendar.get(Calendar.YEAR);
        int mm = calendar.get(Calendar.MONTH);
        int dd = calendar.get(Calendar.DAY_OF_MONTH);
        final DatePickerDialog mDatePicker = new DatePickerDialog(getActivity(), this, yy, mm, dd);
        Date today = new Date();
        Calendar c = Calendar.getInstance(Locale.ENGLISH);
        c.setTimeInMillis(System.currentTimeMillis());
        Calendar c1 = Calendar.getInstance(Locale.ENGLISH);
        c1.setTimeInMillis(System.currentTimeMillis());
        c1.add(Calendar.DAY_OF_MONTH, -((dd - 1) + 31));
        long minDate = c1.getTime().getTime();
        long maxDate = c.getTime().getTime();
        mDatePicker.getDatePicker().setMinDate(minDate);
        mDatePicker.getDatePicker().setMaxDate(maxDate);
        return mDatePicker;

    }

    public void onDateSet(DatePicker view, int yy, int mm, int dd) {
        populateSetDate(yy, mm + 1, dd);
    }

    public void populateSetDate(int year, int month, int day) {
        if (value == 2) {
            mListener.onStartDateSelected(year + "-" + String.format("%02d", month) + "-" + String.format("%02d", day),value);
        } else if (value == 3) {
            mListener.onEndDateSelected(year + "-" + String.format("%02d", month) + "-" + String.format("%02d", day),value);
        } else if (value == 4) {
            mListener.onStartDateSelected(year + "-" + String.format("%02d", month) + "-" + String.format("%02d", day),value);
        } else if (value == 5) {
            mListener.onEndDateSelected(year + "-" + String.format("%02d", month) + "-" + String.format("%02d", day),value);
        }
    }

    public interface myDialogListener {

        void onStartDateSelected(String startDate, int value);

        void onEndDateSelected(String startDate, int value);
    }

}