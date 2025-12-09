package com.dhanuka.indhan.Activity

import android.os.Bundle
import android.view.View

import com.dhanuka.indhan.Activity.ReportsMVP.Model.ReportsBean
import com.dhanuka.indhan.Activity.ReportsMVP.ReportsContracts
import com.dhanuka.indhan.Activity.ReportsMVP.ReportsPresenterImpl
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_reports.*

/**
 * Created by Shivam on 2/28/2018.
 */

class ReportsActivity : BaseActivity(), ReportsContracts.IReportsView {
    private var Token = ""
    private lateinit var mreportsPresenter: ReportsContracts.ReportsPresenter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_reports)
        Token = Preference.getInstance(this).getString("token")
        mreportsPresenter = ReportsPresenterImpl(this)
        ivBack.setOnClickListener(this)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            mreportsPresenter.onSubmitClick(Token)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
        }
    }

    override fun hideProgress() {
        dismissProgress()
    }

    override fun setItems(items: ReportsBean) {
        if (items.status_code == 200) {
            tvMeActivityCount.text = items.data?.total_Activities
            tvMeFarmersCount.text = items.data?.total_Farmers
            tvMeAverageCount.text = items.data?.average_Farmers_Activities
        } else if (items.status_code == 409) {
            val preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, true)
        } else {
            makeToast(items.message)
        }
    }

    override fun onBackPressed() {
        super.onBackPressed()
    }
}
