package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment

import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import com.dhanuka.indhan.Activity.offlineClaimTicket.ManualTicketClaim
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.dialog_select_farmer.*


class SelectArrivalDepartureDialog(context: Context) : Dialog(context) {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(
            LayoutInflater.from(context).inflate(R.layout.dialog_select_arrival_departure, null)
        )
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )
        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)
        //apply click on cancel button
        txtCancel.setOnClickListener {
            dismiss()
        }
        //apply click for new
        txtCreateNewRequest.setOnClickListener {
            val intent = Intent(context, ManualTicketClaim::class.java)
            intent.putExtra("from_ticket", false)
            intent.putExtra("isDeparture", true)
            context.startActivity(intent)
            dismiss()
        }
        //select existing farmer
        txtSelectExistingFarmer.setOnClickListener {
            val intent = Intent(context, ManualTicketClaim::class.java)
            intent.putExtra("from_ticket", false)
            intent.putExtra("isDeparture", false)
            context.startActivity(intent)
            dismiss()
        }


    }


}