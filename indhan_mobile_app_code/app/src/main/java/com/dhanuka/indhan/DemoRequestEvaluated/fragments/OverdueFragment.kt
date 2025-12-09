package com.dhanuka.indhan.DemoRequestEvaluated.fragments

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Bundle
import android.os.Handler
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoRequestEvaluatedActivity
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.CommonDemoRequestAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedlist
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.google.gson.Gson
import kotlinx.android.synthetic.main.fragment_overdue.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class OverdueFragment : Fragment() {
    lateinit var adapter: CommonDemoRequestAdapter
    private lateinit var preference: Preference
    var demoRequestList: ArrayList<DemoRequestEvaluatedlist> = ArrayList()
    lateinit var context: DemoRequestEvaluatedActivity
    var checkedUserId = ""
    var cropId = ""
    var productId = ""
    var fromDate = ""
    var todate = ""
    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        context = requireContext() as DemoRequestEvaluatedActivity
        preference = Preference(context)
        // Inflate the layout for this fragment
        val view = inflater?.inflate(R.layout.fragment_overdue, container, false)
        registerBroadcast()
        return view
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        callDemoRequestDataApi("Overdue")

        swipeLayout.setOnRefreshListener {
            swipeLayout.isRefreshing = false
            callDemoRequestDataApi("Overdue")
            val intent = Intent("Data")
            requireActivity().sendBroadcast(intent)
        }
    }

    private fun applyClickOnCheckBox() {
        chbExcellent?.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                chbExcellent?.isChecked = true
                chbAverage?.isChecked = false
                chbPoor?.isChecked = false
                callDemoRequestDataApi("Excellent")
            } else {
                chbExcellent?.isChecked = false
            }

        }

        chbAverage?.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                callDemoRequestDataApi("Average")

                chbAverage?.isChecked = true
                chbExcellent?.isChecked = false
                chbPoor?.isChecked = false
            } else {
                chbAverage?.isChecked = false
            }

        }
        chbPoor?.setOnCheckedChangeListener { buttonView, isChecked ->
            if (isChecked) {
                callDemoRequestDataApi("Poor")
                chbPoor?.isChecked = true
                chbExcellent?.isChecked = false
                chbAverage?.isChecked = false
            } else {
                chbPoor?.isChecked = false
            }


        }

    }

    private fun registerBroadcast() {
        LocalBroadcastManager.getInstance(context)
            .registerReceiver(broadcastReceiverFilter, IntentFilter("Overdue"))

    }

    var broadcastReceiverFilter = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            if (intent?.hasExtra(Constants.CHECKED_USER_ID)!!) {
                checkedUserId = intent.getStringExtra(Constants.CHECKED_USER_ID).toString()
            }
            cropId = intent?.getStringExtra("cropid").toString()
            productId = intent?.getStringExtra("productid").toString()
            fromDate = intent?.getStringExtra("fromdate").toString()
            todate = intent?.getStringExtra("todate").toString()
            callDemoRequestDataApi("Overdue")
        }


    }

    private fun callDemoRequestDataApi(status: String) {
        context.showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("status", status)
        query.put("from_date", fromDate)
        query.put("to_date", todate)
        query["product"] = productId
        query.put("crop", cropId)
        query.put("dd_ids", checkedUserId)
        var gson = Gson()
        var j = gson.toJson(query)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getDemoRequestEvaluated(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoRequestEvaluatedResponse>() {
                override fun onCompleted() {
                    context.hideProgress()
                }

                override fun onError(e: Throwable) {
                    context.hideProgress()
//                        context.makeToast("Not able to connect 1")
                }

                override fun onNext(listBean: DemoRequestEvaluatedResponse) {
                    context.hideProgress()
                    if (listBean.status_code == 200) {
                        demoRequestList.clear()
                        demoRequestList.addAll(listBean.data.demolist)
                        if (demoRequestList.size > 0) {
                            txtEmpty.visibility = View.GONE
                            // llHeader.visibility=View.VISIBLE
                            setCommonAdapter(demoRequestList)
                        } else {
                            txtEmpty.visibility = View.VISIBLE
                            // llHeader.visibility=View.GONE
                            setCommonAdapter(demoRequestList)

                        }

                    } else {

                    }
                }
            })

    }

    private fun setCommonAdapter(list: ArrayList<DemoRequestEvaluatedlist>) {
        recyclerViewAwaited.layoutManager =
            LinearLayoutManager(context)
        adapter = CommonDemoRequestAdapter(context, list)
        recyclerViewAwaited.adapter = adapter

    }

    override fun onResume() {
        super.onResume()

    }

    override fun setUserVisibleHint(isVisibleToUser: Boolean) {
        super.setUserVisibleHint(isVisibleToUser)
        if (isVisibleToUser == true) {
            Handler().postDelayed({
                // callDemoRequestDataApi("Overdue")
                //apply click on checkbox
                applyClickOnCheckBox()
            }, 100)
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        LocalBroadcastManager.getInstance(context).unregisterReceiver(broadcastReceiverFilter)
    }
}