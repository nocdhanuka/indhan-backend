package com.dhanuka.indhan.util;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.fragment.app.DialogFragment;

import com.dhanuka.indhan.Interfaces.ConfirmDialogListener;

public class AlertDialogFragment extends DialogFragment {

    private static final String ARGS_TITLE = "title";
    private static final String ARGS_MESSAGE = "message";
    private static final String ARGS_REQUEST_CODE = "request_code";

    private ConfirmDialogListener mListener;

    public static AlertDialogFragment newInstance(String message) {
        AlertDialogFragment frag = new AlertDialogFragment();
        Bundle args = new Bundle();
        args.putString(ARGS_MESSAGE, message);

        frag.setArguments(args);
        return frag;
    }

    public static AlertDialogFragment newInstance(String title, String message) {
        AlertDialogFragment frag = new AlertDialogFragment();
        Bundle args = new Bundle();
        args.putString(ARGS_TITLE, title);
        args.putString(ARGS_MESSAGE, message);

        frag.setArguments(args);
        return frag;
    }

    public static AlertDialogFragment newInstance(String title, String message, int requestCode) {
        AlertDialogFragment frag = new AlertDialogFragment();
        Bundle args = new Bundle();
        args.putString(ARGS_TITLE, title);
        args.putString(ARGS_MESSAGE, message);
        args.putInt(ARGS_REQUEST_CODE, requestCode);

        frag.setArguments(args);
        return frag;
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
        if (getTargetFragment() == null) {
            try {
                mListener = (ConfirmDialogListener) activity;
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
                mListener = (ConfirmDialogListener) getTargetFragment();
            } catch (ClassCastException e) {
                throw new ClassCastException("Calling fragment must implement DialogClickListener interface");
            }
        }
    }

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        String title = "";
        if (getArguments().getString(ARGS_TITLE) != null) {
            title = getArguments().getString(ARGS_TITLE);
        }
        String message = getArguments().getString(ARGS_MESSAGE);

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        if (title.length() > 0) {
            builder.setTitle(title);
        }
        builder.setMessage(message);
        builder.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                if (mListener != null) {
                    int requestCode = getArguments().getInt(ARGS_REQUEST_CODE);
                    mListener.onPositiveClick(null, requestCode);
                }
            }
        });

        return builder.create();
    }
}