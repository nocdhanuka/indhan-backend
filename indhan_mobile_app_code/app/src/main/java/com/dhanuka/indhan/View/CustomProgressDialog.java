package com.dhanuka.indhan.View;

import android.app.Dialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;

import com.dhanuka.indhan.R;


public class CustomProgressDialog extends Dialog {
    LayoutInflater inflater;
    View v;

    public CustomProgressDialog(Context context) {
        super(context, android.R.style.Theme_Holo_Dialog_NoActionBar);
        inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        v = inflater.inflate(R.layout.custom_progrees_bar, null);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        setContentView(v);
        setCancelable(false);
    }
}
