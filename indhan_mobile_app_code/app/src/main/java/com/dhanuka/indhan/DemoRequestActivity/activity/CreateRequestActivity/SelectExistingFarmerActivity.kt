package com.dhanuka.indhan.DemoRequestActivity.activity.CreateRequestActivity

import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.adapters.FarmerExistingAdapter
import com.dhanuka.indhan.DemoRequestActivity.models.ExistingData
import com.dhanuka.indhan.DemoRequestActivity.models.ExistingFarmerListResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.activity_select_existing_farmer.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class SelectExistingFarmerActivity : BaseActivity() {


    private lateinit var preference: Preference
    private var existingFarmerList: ArrayList<ExistingData> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_select_existing_farmer)
        intent?.let {
            if (it.hasExtra(Constants.VILLAGE_ID)) {
                val villageId = it.getStringExtra(Constants.VILLAGE_ID) ?: ""
                getExistingFarmerList(villageId)
            }
        }

    }


    private fun getExistingFarmerList(villageId: String) {
        preference = Preference(this)

        /*
         *get data and send to Api
         * */
        val query = HashMap<String, Any>()
        query["villageId"] = villageId
        query["apiToken"] = preference.getString("token")
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).getExistingFarmerList(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<ExistingFarmerListResponse>() {
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

                    override fun onNext(villageBean: ExistingFarmerListResponse) {
                        hideProgress()
                        if (!villageBean.data.isNullOrEmpty()) {
                            txtEmpty.visibility = View.GONE
                            existingFarmerList.clear()
                            existingFarmerList.addAll(villageBean.data)
                            //set data on adapter
                            setDataOnAdapter(existingFarmerList)
                        } else {
                            txtEmpty.visibility = View.VISIBLE
                        }

                    }
                }
                )
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun setDataOnAdapter(existingFarmerList: ArrayList<ExistingData>) {
        // Creates a vertical Layout Manager
        recyclerViewFarmer.layoutManager =
            LinearLayoutManager(this@SelectExistingFarmerActivity)
        val adapter = FarmerExistingAdapter(
            this@SelectExistingFarmerActivity,
            existingFarmerList,
            object : OnSuccess {
                override fun onClick(data: String) {
                    if (data.equals("back")) {
                        this@SelectExistingFarmerActivity.finish()
                    }
                }
            })
        recyclerViewFarmer.adapter = adapter
    }

    override fun onClick(v: View?) {

    }
}
