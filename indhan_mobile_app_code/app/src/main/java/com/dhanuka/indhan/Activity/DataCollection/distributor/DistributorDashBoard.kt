package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Model.AllDistDistrictReponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_distributor_dashboard.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class DistributorDashBoard : BaseActivity(), View.OnClickListener,
    DistrictWiseItemAdapter.OnDistrictSelected {
    private val districtList = ArrayList<AllDistDistrictReponse.Data>()
    private lateinit var preference: Preference
    private lateinit var listAdapter: DistrictWiseItemAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_distributor_dashboard)
        preference = Preference(this)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getDistricts()
        } else {
            makeToast("No Internet Connection.Please check your connection.")
        }
        setListeners()
    }

    private fun getDistricts() {
        showProgress()
        val query = HashMap<String, Any>()
        query["ecnnumber"] = preference.getString("ecn")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getAllDistricts(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AllDistDistrictReponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: AllDistDistrictReponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        districtList.clear()
                        districtList.addAll(listBean.data)
                        setDistrictData()
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    private fun setDistrictData() {
        val layoutManager =
            LinearLayoutManager(
                this@DistributorDashBoard,
                LinearLayoutManager.VERTICAL,
                false
            )
        rcvDistrict.layoutManager = layoutManager
        listAdapter = DistrictWiseItemAdapter(districtList, this@DistributorDashBoard)
        rcvDistrict.adapter = listAdapter
    }

    private fun setListeners() {

    }

    override fun onClick(v: View?) {

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == Constants.ACTIVITY_RESULT_DISTRICT) {
                getDistricts()
            }
        }
    }

    override fun onItemClick(position: Int) {
        val districtObject: AllDistDistrictReponse.Data = districtList[position]
        val bundle = Bundle()
        bundle.putString("district_id", districtObject.district_id.toString())
        bundle.putString("district_name", districtObject.district_name)
        bundle.putString("state_id", districtObject.state_id)
        val intent = Intent(activity, DistributorsDataCollectionActivity::class.java)
        intent.putExtras(bundle)
        startActivityForResult(intent, Constants.ACTIVITY_RESULT_DISTRICT)
    }
}