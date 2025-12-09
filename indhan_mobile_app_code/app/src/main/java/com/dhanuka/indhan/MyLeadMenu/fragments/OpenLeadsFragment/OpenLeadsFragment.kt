package com.dhanuka.indhan.MyLeadMenu.fragments.OpenLeadsFragment

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.net.Uri
import android.os.Bundle
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity.MyLeadActivity
import com.dhanuka.indhan.MyLeadMenu.adapters.OpenLeadAdapterAdapter
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadData
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadsResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.UpdateCallListener
import com.dhanuka.indhan.validatefarmers.SuccessBean
import com.google.gson.Gson
import kotlinx.android.synthetic.main.fragment_open_leads.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class OpenLeadsFragment : Fragment() {
    lateinit var context: MyLeadActivity
    private lateinit var preference: Preference
    var openList: ArrayList<OpenLeadData> = ArrayList()
    var apiToken: String = ""

    var ddId: String = ""
    lateinit var adapter: OpenLeadAdapterAdapter
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


        ddId = arguments?.getString("ddId").toString()
        context = requireContext() as MyLeadActivity
        // Inflate the layout for this fragment


        val view: View = inflater.inflate(R.layout.fragment_open_leads, container, false)

        return view

    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        preference = Preference(context)

        apiToken = preference.getString("token")
        //get open lead list
        getOpenLeadListApi("yes")
        //register broadcast
        registerBroadcast();


        swipeRefreshLayout.setOnRefreshListener {
            swipeRefreshLayout.isRefreshing = true
            getOpenLeadListApi("yes")
        }

    }

    private fun registerBroadcast() {
        LocalBroadcastManager.getInstance(context)
            .registerReceiver(broadcastReceiver, IntentFilter(Constants.REFRESH))
        LocalBroadcastManager.getInstance(context)
            .registerReceiver(broadcastReceiverFilter, IntentFilter(Constants.FILTER))

    }

    var broadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            var isRefresh = intent?.getStringExtra("isRefresh")
            if (isRefresh.equals("yes")) {
                //get open lead list
                getOpenLeadListApi("no")
            }
        }
    }

    var broadcastReceiverFilter = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            checkedUserId = intent?.getStringExtra("checkedUserId").toString()
            cropId = intent?.getStringExtra("cropid").toString()
            productId = intent?.getStringExtra("productid").toString()
            fromDate = intent?.getStringExtra("fromdate").toString()
            todate = intent?.getStringExtra("todate").toString()
            ddId = checkedUserId
            getOpenLeadListApi("no")

        }


    }

    override fun setUserVisibleHint(isVisibleToUser: Boolean) {
        super.setUserVisibleHint(isVisibleToUser)
        if (isVisibleToUser == true) {
            //get open lead list
            //  if (context!=null)
            // getOpenLeadListApi()
        }
    }

    //get open lead list
    private fun getOpenLeadListApi(isProgress: String) {
        if (isProgress == "yes") {
            context.showProgress()
        }

        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("type", "1")//1 for open lead
        query.put("dd_ids", ddId)//1 for open lead
        query.put("from_date", fromDate)
        query.put("to_date", todate)
        query.put("product_ids", productId)


        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getOpenLead(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<OpenLeadsResponse>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    context.hideProgress()
                    context.makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )

                }

                override fun onNext(openLead: OpenLeadsResponse) {
                    context.hideProgress()
                    if (openLead != null) {
                        if (openLead.status_code == 409) {
                            EventBus.getDefault().post("logout")
                            return
                        }
                    }
                    swipeRefreshLayout.isRefreshing = false
                    if (!openLead.data.isNullOrEmpty()) {
                        txtEmpty.visibility = View.GONE
                        openList.clear()
                        openList.addAll(openLead.data)
                        //set data on adapter
                        setDataOnAdapter(openList)

                    } else {
                        txtEmpty.visibility = View.VISIBLE
                        //set data on adapter
                        openList.clear()
                        setDataOnAdapter(openList)
                    }

                }
            }
            )
    }

    private fun setDataOnAdapter(openList: ArrayList<OpenLeadData>) {
        recyclerViewOpenLead.layoutManager =
            LinearLayoutManager(context)
        adapter = OpenLeadAdapterAdapter(context, openList, apiToken, object : UpdateCallListener {
            override fun onSuccess(
                isYes: String,
                farmerId: String,
                farmerType: String,
                contactNo: String
            ) =
                if (isYes.equals("yes")) {
                    callSatatus(farmerId, farmerType, contactNo)
                } else {
                    val intent = Intent(Intent.ACTION_DIAL)
                    intent.data = Uri.parse("tel:$contactNo")
                    context.startActivity(intent)
                }
        })
        recyclerViewOpenLead.adapter = adapter
        adapter.notifyDataSetChanged()

    }

    //call status api
    private fun callSatatus(farmerId: String, farmerType: String, contactNo: String) {
        context.showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("farmerId", farmerId)
        query.put("farmer_type", farmerType)
        query.put("status", "1")


        val gson = Gson()
        val json = gson.toJson(query)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateCallStatus(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    context.hideProgress()
                    context.makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )

                }

                override fun onNext(openLead: SuccessBean) {
                    context.hideProgress()
                    if (openLead.statusCode == 200) {
                        getOpenLeadListApi("no")
                        var intent = Intent(Intent.ACTION_DIAL)
                        intent.data = Uri.parse("tel:" + contactNo)
                        context.startActivity(intent)
                    }
                }
            })
    }

    override fun onDestroy() {
        super.onDestroy()
        LocalBroadcastManager.getInstance(context).unregisterReceiver(broadcastReceiver)
        LocalBroadcastManager.getInstance(context).unregisterReceiver(broadcastReceiverFilter)

    }


}