package com.dhanuka.indhan.Profile

import android.os.Bundle
import android.view.View
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.activity_profile.*
import org.greenrobot.eventbus.EventBus
import java.util.*

class ProfileActivity : BaseActivity(), ProfileContractor.ProfileView {

    override fun onClick(v: View?) {

    }

    override fun hideProgress() {
        dismissProgress()
    }

    override fun setItems(items: DDProfileBean) {
        setData(items)
    }

    override fun failure(items: String) {
        makeToast(items)
        logEventCustom(
            items,
            "v2/ddprofilesummary"
        )
    }

    private lateinit var mProfilePresenter: ProfileContractor.ProfilePresenter
    private lateinit var preference: Preference
    private lateinit var adapter: ProfileAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_profile)
        mProfilePresenter = ProfilePresenterImpl(this)
        preference = Preference(this)
    }

    override fun onResume() {
        super.onResume()
        val query = HashMap<String, Any>()
        query["ECN"] = preference.getString("ecn")
        query["apiToken"] = preference.getString("token")
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            mProfilePresenter.onGetProfile(query)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun setData(data: DDProfileBean) {
        if (data != null) {
            if (data.statusCode == 200) {
                adapter = ProfileAdapter(this, data, 3)
                viewPager.adapter = adapter

                txtUserName.text = data.data.name
                txtID.text = "ID: " + data.data.ddCode
                txtPhoneNo.text = data.data.ddMobileNo
                txtAltPhoneNo.text = data.data.ddAltMobileNo
                txtEmail.text = data.data.ddEmail
                txtAddress.text = data.data.ddAddress
                txtAverageFarmer.text = data.data.averageFarmer
                if (data.data.ddImage != null) {
                    Glide.with(this)
                        .load(data.data.ddImage)
                        .apply(
                            RequestOptions().placeholder(R.drawable.def_user)
                                .error(R.drawable.def_user)
                        )
//                    .centerCrop()
//                    .crossFade()
                        .into(imgProfile)
                }
            } else if (data.statusCode == 409) {
                EventBus.getDefault().post("logout")
            }
        }

    }

    private fun logEventCustom(mes: String?, fromScreen: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = fromScreen
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("ProfileActivity", "failed_api_$fromScreen", bundle)
    }
}
