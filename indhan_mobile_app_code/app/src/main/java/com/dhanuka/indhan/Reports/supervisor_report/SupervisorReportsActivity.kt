package com.dhanuka.indhan.Reports.supervisor_report

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.View.FilterActivity
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.activity_supervisor_reports.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class SupervisorReportsActivity : BaseActivity() {
    override fun onClick(v: View?) {
    }


    private lateinit var adapter: ReportsAdapter
    private lateinit var preference: Preference

    var list_efficieny: ArrayList<EfficiencyResponse.Efficiency> = ArrayList()
    var curr = 0
    var prev = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_supervisor_reports)
        preference = Preference(this)

        /*
        * View Pager
        * */
        tabSetup()

        //call get reports
        getReports(preference.getString("token"))

        imgFilter.setOnClickListener {
            val intent = Intent(this, FilterActivity::class.java)
//            b.putString("startDate", startDate)
//            b.putString("endDate", endDate)
//            b.putString("manager_level", manager_level)
//            b.putInt("product", product)
//            b.putString("product_name", product_name)
//            b.putInt("manager_id", manager_id)
//            b.putString("manager_name", manager_name)
//            b.putInt("sr_manager_id", sr_manager_id)
//            b.putString("sr_manager_name", sr_manager_name)
//            b.putInt("s1_id", s1_id)
//            b.putString("s1_name", s1_name)
            intent.putExtra("from", "")
            startActivityForResult(intent, 1001)
        }


    }

    /*
       * viewPager layout setup
       * */
    private fun tabSetup() {

//       viewPager.setPagingEnabled(false)
    }


    /*
    * call getReportApi
    * */
    private fun getReports(token: String) {
        showProgress()
        val query = java.util.HashMap<String, Any>()
        query["apiToken"] = token
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).supervisorReport(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SupervisorReportsBean>() {
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

                override fun onNext(supervisorReportsBean: SupervisorReportsBean) {
                    hideProgress()
                    if (supervisorReportsBean.statusCode == 200) {
                        setDataOnView(supervisorReportsBean.data)
                        adapter = ReportsAdapter(supportFragmentManager, supervisorReportsBean.data)
                        viewPager.adapter = adapter
                        viewPager.offscreenPageLimit = 4

                    } else if (supervisorReportsBean.statusCode == 409) {
                        EventBus.getDefault().post("logout")
                    }
                }
            })
    }

    private fun setDataOnView(data: SupervisorReportsBean.Data) {
        txtActivities.text = data.activities.toString()
        txtVillageMeetings.text = data.totalVillageMeeting.toString()
        txtFieldDays.text = data.totalFieldDay.toString()
        txtFarmers.text = data.totalRegFarmer.toString()
        txtActivitiesBDFL.text = data.bdFlActivity.toString()
        txtActivitiesSFL.text = data.sFlActivity.toString()
        txtAvgFarmer.text = data.averageFarmerActivity.toString()
        txtDemos.text = data.democount.toString()

    }


}

