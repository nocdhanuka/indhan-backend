package com.dhanuka.indhan.ATPMenu.activity.activities

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.activity_register_farmer.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class RegisterFarmerActivity : BaseActivity() {
    var phoneNo: String = ""
    private lateinit var preference: Preference
    var atpPlanId: String = ""
    var ticketId: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_farmer)
        preference = Preference(this)
        intent?.let {
            if (it.hasExtra(Constants.PLAN_ATP_ID)) {
                atpPlanId = it.getStringExtra(Constants.PLAN_ATP_ID) ?: ""
            }
            if (it.hasExtra(Constants.TICKET_ID)) {
                ticketId = it.getStringExtra(Constants.TICKET_ID) ?: ""
            }
        }
        //apply click on view
        applyClickOnView()

    }

    //apply click on view
    private fun applyClickOnView() {
        llSubmit.setOnClickListener {
            if (validateForm()) {
                registerFarmerApi()
            }
        }
    }

    //register farmer api
    private fun registerFarmerApi() {
        showProgress()
        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query["apiToken"] = model
        query["plan_atp_id"] = atpPlanId
        query["contact_number"] = phoneNo
        query["ticket_id"] = ticketId


        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getRegisterFarmer(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(commonBean: CommonAtpResponse) {
                    hideProgress()
                    if (commonBean.status_code == 200) {
                        makeToast(commonBean.message)
                        finish()
                    }
                }
            })
    }
    //end

    //validate form
    private fun validateForm(): Boolean {
        phoneNo = edtContactsNo.text.toString()
        if (TextUtils.isEmpty(phoneNo)) {
            makeToast(resources.getString(R.string.err_contact_no))
            return false
        } else if (phoneNo.length < 10) {
            makeToast(resources.getString(R.string.err_contact_valid))
            return false
        }
        return true

    }

    override fun onClick(v: View?) {

    }
}
