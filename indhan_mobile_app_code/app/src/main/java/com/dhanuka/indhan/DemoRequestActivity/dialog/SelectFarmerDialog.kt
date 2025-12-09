package com.dhanuka.indhan.DemoRequestActivity.dialog

import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import com.dhanuka.indhan.DemoRequestActivity.activity.CreateRequestActivity.CreateRequestActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.dialog_select_farmer.*


class SelectFarmerDialog(context: Context, onSuccess: OnSuccess) : Dialog(context) {
    var onSuccess: OnSuccess

    init {
        this.onSuccess = onSuccess
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(LayoutInflater.from(context).inflate(R.layout.dialog_select_farmer, null))
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )
        // this.window!!.setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN)
        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)
        //apply click on cancel button
        txtCancel.setOnClickListener {
            dismiss()
        }
        //apply click for new
        txtCreateNewRequest.setOnClickListener {
            val intent = Intent(context, CreateRequestActivity::class.java)
            context.startActivity(intent)
            dismiss()
        }
        //select existing farmer
        txtSelectExistingFarmer.setOnClickListener {
            /* *//* val intent=Intent(context,SelectExistingFarmerActivity::class.java)
             context.startActivity(intent)
             dismiss()*//*
            showDdVillageListDialog()*/
            onSuccess.onClick("yes")
            dismiss()
        }


    }


}