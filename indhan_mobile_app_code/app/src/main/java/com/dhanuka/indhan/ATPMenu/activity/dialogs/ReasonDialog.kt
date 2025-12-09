package com.dhanuka.indhan.ATPMenu.activity.dialogs

import android.app.Dialog
import android.content.Context
import android.os.Bundle
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import android.widget.Toast
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.dialog_reason_atp.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ReasonDialog(context: Context) : Dialog(context) {
    private lateinit var preference: Preference
    lateinit var reason: String


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(LayoutInflater.from(context).inflate(R.layout.dialog_reason_atp, null))
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )
        // this.window!!.setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN)
        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        preference = Preference(context)
        //apply click on view
        applyCLickOnView()

    }

    //apply click on view
    private fun applyCLickOnView() {
        txtCloseAtp.setOnClickListener {
            if (validateDialogForm()) {
                closeAtpApi()
            }
        }
    }


    //reason for close atp activation api
    private fun closeAtpApi() {
        // context.showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("plan_atp_id", "")
        //  query.put("contact_number",phoneNo)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).closeAtpApi(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    //hideProgress()
                }

                override fun onError(e: Throwable) {
                    //hideProgress()
                    //makeToast(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(commonBean: CommonAtpResponse) {
                    // hideProgress()
                    if (commonBean.status_code == 200) {
                        //  makeToast(commonBean.message)
                        //  finish()
                    }
                }
            })
    }

    //validate dialog form
    private fun validateDialogForm(): Boolean {
        reason = edtReason.text.toString()
        if (TextUtils.isEmpty(reason)) {
            Toast.makeText(context, context.getString(R.string.err_reason), Toast.LENGTH_LONG)
                .show()
            return false
        }
        return true
    }

}