package com.dhanuka.indhan.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import kotlinx.android.synthetic.main.activity_activities.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class ActivitiesActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }


    private lateinit var preference: Preference
    private lateinit var adapter: ActivitiesAdapter

    private var activitiesList = mutableListOf<ActivitiesBean.Data.Activity>()
    private lateinit var data: ActivitiesBean
    private var startDate = ""
    private var endDate = ""
    private var crop = ""
    private var product = ""
    private var dd = ""
    private var fl = ""
    private var dataSourece = ""
    private var rsmId = ""
    private var asmId = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activities)
        preference = Preference(this)

        getAcitivitiesList()

        imgFilter.setOnClickListener {
            var i = Intent(this, ActivitiesFilterActivity::class.java)
            startActivityForResult(i, 100)
        }

    }

    /*
   * get DD list
   * */
    private fun getAcitivitiesList() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["dd_ids"] = dd
        query["data_source_id"] = dataSourece
        query["from_date"] = startDate
        query["to_date"] = endDate
        query["crop"] = crop
        query["product"] = product
        query["fl_ids"] = fl
        query["asm"] = asmId
        query["rsm"] = rsmId



        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).BdoActivityList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ActivitiesBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: ActivitiesBean) {
                    hideProgress()

                    if (listBean.statusCode == 200) {

                        activitiesList.clear()
                        data = listBean
                        activitiesList.addAll(listBean.data.activities)
                        adapter = ActivitiesAdapter(this@ActivitiesActivity, activitiesList)
                        recyclerViewActivities.layoutManager =
                            LinearLayoutManager(this@ActivitiesActivity)
                        recyclerViewActivities.adapter = adapter

                        txtTotalACP.text = data.data.totalFarmer.toString()
                        txtTotalAcrerageValue.text = data.data.totalVillage.toString()
                        txtTotalLeadValue.text = data.data.totalActivity.toString()

                    } else {
                        if (listBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })

    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == 100 && requestCode == 100) {
            data?.let {
                startDate = it.getStringExtra("startDate") ?: ""
                endDate = it.getStringExtra("endDate") ?: ""
                dd = it.getStringExtra("dd") ?: ""
                fl = it.getStringExtra("fl") ?: ""
                crop = it.getStringExtra("crop") ?: ""
                product = it.getStringExtra("product") ?: ""
                dataSourece = it.getStringExtra("dataSource") ?: ""
                rsmId = it.getStringExtra("rsm") ?: ""
                asmId = it.getStringExtra("asm") ?: ""
                getAcitivitiesList()
            }
        }
    }
}
