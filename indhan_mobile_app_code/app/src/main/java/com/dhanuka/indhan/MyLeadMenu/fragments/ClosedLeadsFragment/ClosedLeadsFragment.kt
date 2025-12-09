package com.dhanuka.indhan.MyLeadMenu.fragments.ClosedLeadsFragment

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Bundle
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity.MyLeadActivity
import com.dhanuka.indhan.MyLeadMenu.adapters.CloseLeadAdapterAdapter
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadData
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadsResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.google.gson.Gson
import kotlinx.android.synthetic.main.fragment_closed_leads.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers


class ClosedLeadsFragment : Fragment() {
    lateinit var context: MyLeadActivity
    private lateinit var preference: Preference
    var closeLead: ArrayList<OpenLeadData> = ArrayList()
    var ddId: String = ""
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
        context = requireContext() as MyLeadActivity
        // Inflate the layout for this fragment

        ddId = arguments?.getString("ddId").toString()
        val view = inflater?.inflate(R.layout.fragment_closed_leads, container, false)
        registerBroadcast()
        return view

    }

    private fun registerBroadcast() {
        LocalBroadcastManager.getInstance(context)
            .registerReceiver(broadcastReceiverFilter, IntentFilter(Constants.FILTER_CLOSE))

    }

    var broadcastReceiverFilter = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            checkedUserId = intent?.getStringExtra("checkedUserId").toString()
            cropId = intent?.getStringExtra("cropid").toString()
            productId = intent?.getStringExtra("productid").toString()
            fromDate = intent?.getStringExtra("fromdate").toString()
            todate = intent?.getStringExtra("todate").toString()
            ddId = checkedUserId
            getCloseLeadListApi()

        }


    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        preference = Preference(context)

        //get close lead list
        //  getCloseLeadListApi()

    }

    override fun setUserVisibleHint(isVisibleToUser: Boolean) {
        super.setUserVisibleHint(isVisibleToUser)
        if (isVisibleToUser == true) {
            //get close lead list
            getCloseLeadListApi()
        }
    }

    //get close lead list
    private fun getCloseLeadListApi() {
        context.showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("type", "2")//2 for close lead
        query.put("dd_ids", ddId)//1 for open lead
        query.put("from_date", fromDate)
        query.put("to_date", todate)
        query.put("product_ids", productId)

        val gson = Gson()
        val json = gson.toJson(query)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getOpenLead(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<OpenLeadsResponse>() {
                override fun onCompleted() {
                    context.hideProgress()
                }

                override fun onError(e: Throwable) {
                    context.hideProgress()
                    context.makeToast(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")

                }

                override fun onNext(openLead: OpenLeadsResponse) {
                    context.hideProgress()
                    if (!openLead.data.isNullOrEmpty()) {
                        txtEmpty.visibility = View.GONE
                        closeLead.clear()
                        closeLead.addAll(openLead.data)
                        //pupulate data on cell
                        setDataOnAdapter(closeLead)
                    } else {
                        txtEmpty.visibility = View.VISIBLE
                        //pupulate data on cell
                        closeLead.clear()
                        setDataOnAdapter(closeLead)
                    }

                }
            }
            )
    }

    private fun setDataOnAdapter(closeLead: ArrayList<OpenLeadData>) {
        recyclerViewCloseLead.layoutManager =
            LinearLayoutManager(context)
        var adapter = CloseLeadAdapterAdapter(context, closeLead)
        recyclerViewCloseLead.adapter = adapter

    }

    override fun onDestroy() {
        super.onDestroy()
        LocalBroadcastManager.getInstance(context).unregisterReceiver(broadcastReceiverFilter)
    }
}