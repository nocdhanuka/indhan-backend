package com.dhanuka.indhan.ATPMenu.activity.activities

import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.adapters.InvitedFarmerAdapter
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_invited_farmer_list.*
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class InvitedFarmerListActivity : BaseActivity() {
    override fun onClick(v: View?) {
    }

    var inviFarmerList: ArrayList<ValidatedfarmerListResponse.Data> = ArrayList()
    var planId: String = ""

    private lateinit var preference: Preference

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_invited_farmer_list)
        preference = Preference(this)
        planId = intent?.getStringExtra("atpId") ?: ""
        if (CheckConnectionStatus.isInternetAvailable(this))
            inviteFarmerListApi()
        else {
            getFarmerListFromDB()
        }
    }

    private fun getFarmerListFromDB() {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            inviFarmerList.clear()
            inviFarmerList = appDatabase.atpFarmerDao()
                .getAllFarmerList(planId.toInt()) as ArrayList<ValidatedfarmerListResponse.Data>
            setInvitedFarmerList(inviFarmerList)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    //invite farmer api
    private fun inviteFarmerListApi() {
        showProgress()
        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("plan_atp_id", planId)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .validateFarlerListApi(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ValidatedfarmerListResponse>() {
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

                override fun onNext(commonBean: ValidatedfarmerListResponse) {
                    hideProgress()
                    if (commonBean.statusCode == 200) {
                        inviFarmerList.clear()
                        inviFarmerList.addAll(commonBean.data)
                        setInvitedFarmerList(inviFarmerList)
                    }
                }
            })
    }

    private fun setInvitedFarmerList(data: ArrayList<ValidatedfarmerListResponse.Data>) {
        recyclerView.layoutManager =
            LinearLayoutManager(this@InvitedFarmerListActivity)
        val adapter = InvitedFarmerAdapter(this@InvitedFarmerListActivity, data)
        recyclerView.adapter = adapter
    }
}
