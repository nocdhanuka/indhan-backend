package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment

import android.app.Activity
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Bundle
import android.os.Handler
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPActivity
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.PaginationListener.PAGE_START
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DataBaseHelperNew
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.SupervisorATPListAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.dhanuka.indhan.validatefarmers.SuccessBean
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.fragment_atllist.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set


class ATLListFragment : Fragment() {


    private lateinit var mContext: SupervisorATPActivity
    private lateinit var preference: Preference
    private lateinit var adapter: SupervisorATPListAdapter

    private var productId = ""
    private var cropId = ""
    private var fromDate = ""
    private var endDate = ""
    private var rsmId = ""
    private var asmId = ""
    private var flId = ""
    private var ddId = ""
    private val filteredData = ArrayList<ATPBean.Data>()
    private lateinit var linearLayout: LinearLayoutManager
    private lateinit var dataBaseHelperNew: DataBaseHelperNew
    private var currentPage = PAGE_START
    private var mIsLastPage = false
    private var totalPage = 30
    private var mIsLoading = false
    var itemCount = 0

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        mContext = requireContext() as SupervisorATPActivity
        preference = Preference(mContext)
        dataBaseHelperNew = DataBaseHelperNew(mContext)
        linearLayout = LinearLayoutManager(mContext)
        // Inflate the layout for this fragment


        return inflater.inflate(R.layout.fragment_atllist, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        swipeLayout.setOnRefreshListener {
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["rsm"] = rsmId
            query["asm"] = asmId
            query["bdo_id"] = flId
            query["dd_id"] = ddId
            query["product"] = productId
            query["crop"] = cropId
            query["from_date"] = fromDate
            query["to_date"] = endDate
            query["offset"] = 0
            itemCount = 0


            if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
                getATPListAPI(query)
//            } else {
//                if(dataBaseHelperNew.getATPList(mContext).data!=null){
//                    filteredData.addAll(dataBaseHelperNew.getATPList(mContext).data)
//                    if(filteredData.size>0){
//                        txtNoTickets.visibility=View.GONE
//                    adapter = SupervisorATPListAdapter(filteredData, mContext, object : OnClickListener {
//                        override fun onClick(position: Int) {
//
//                            val query = HashMap<String, Any>()
//                            query["apiToken"] = preference.getString("token")
//                            query["plan_atp_id"] = filteredData[position].plan_atp_id
//
//                            if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
//                                addMyATPAPI(query)
//
//                            } else {
//                                mContext.makeToast(resources.getString(R.string.no_internet_connection))
//                            }
//                        }
//                    })
//                    recyclerViewATP.layoutManager = linearLayout
//                    recyclerViewATP.adapter = adapter
//                }else{
//                        txtNoTickets.visibility=View.VISIBLE
//
//                    }}
//                mContext.makeToast(resources.getString(R.string.no_internet_connection))
            }
            swipeLayout.isRefreshing = false

        }
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {

        super.onActivityCreated(savedInstanceState)

        /**
         * add scroll listener while user reach in bottom load more will call
         */
        recyclerViewATP.addOnScrollListener(object : PaginationListener(linearLayout) {
            override fun isLastPage(): Boolean {
                return mIsLastPage
            }

            override fun loadMoreItems() {
                mIsLoading = true
                currentPage++
                val query = HashMap<String, Any>()
                query["apiToken"] = preference.getString("token")
                query["rsm"] = rsmId
                query["asm"] = asmId
                query["bdo_id"] = flId
                query["dd_id"] = ddId
                query["product"] = productId
                query["crop"] = cropId
                query["from_date"] = fromDate
                query["to_date"] = endDate
                query["offset"] = itemCount
                getATPListAPI(query)
            }

            override fun isLoading(): Boolean {
                return mIsLoading
            }
        })




        LocalBroadcastManager.getInstance(mContext)
            .registerReceiver(mMessageReceiver, IntentFilter("custom-event-name"))
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["rsm"] = rsmId
        query["asm"] = asmId
        query["bdo_id"] = flId
        query["dd_id"] = ddId
        query["product"] = productId
        query["crop"] = cropId
        query["from_date"] = fromDate
        query["to_date"] = endDate
        query["offset"] = 0

        if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            if (dataBaseHelperNew.getATPList(mContext).data != null) {
                filteredData.addAll(dataBaseHelperNew.getATPList(mContext).data)
                if (filteredData.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter =
                        SupervisorATPListAdapter(filteredData, mContext, object : OnClickListener {
                            override fun onClick(position: Int) {
                                val query = HashMap<String, Any>()
                                query["apiToken"] = preference.getString("token")
                                query["plan_atp_id"] = filteredData[position].plan_atp_id
                                if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
                                    addMyATPAPI(query)
                                } else {
                                    mContext.makeToast(resources.getString(R.string.no_internet_connection))
                                }
                            }
                        })

                    recyclerViewATP.layoutManager =
                        LinearLayoutManager(mContext)
                            .apply { isAutoMeasureEnabled = false }
                    recyclerViewATP.adapter = adapter
                } else {
                    txtNoTickets.visibility = View.VISIBLE

                }
            }
            getATPListAPI(query)

        } else {
            if (dataBaseHelperNew.getATPList(mContext).data != null) {
                filteredData.addAll(dataBaseHelperNew.getATPList(mContext).data)
                if (filteredData.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter =
                        SupervisorATPListAdapter(filteredData, mContext, object : OnClickListener {
                            override fun onClick(position: Int) {

                                val query = HashMap<String, Any>()
                                query["apiToken"] = preference.getString("token")
                                query["plan_atp_id"] = filteredData[position].plan_atp_id

                                if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
                                    addMyATPAPI(query)

                                } else {
                                    mContext.makeToast(resources.getString(R.string.no_internet_connection))
                                }
                            }
                        })

                    recyclerViewATP.layoutManager =
                        LinearLayoutManager(mContext)
                            .apply { isAutoMeasureEnabled = false }
                    recyclerViewATP.adapter = adapter
                } else {

                    txtNoTickets.visibility = View.VISIBLE

                }
            }
            mContext.makeToast(resources.getString(R.string.no_internet_connection))
        }


    }


    private val mMessageReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context, data: Intent?) {
            data?.let {
                cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                endDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                ddId = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
                rsmId = it.getStringExtra("RSMID") ?: ""
                asmId = it.getStringExtra("ASMID") ?: ""
                flId = it.getStringExtra("FLID") ?: ""
            }

            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["rsm"] = rsmId
            query["asm"] = asmId
            query["bdo_id"] = flId
            query["dd_id"] = ddId
            query["product"] = productId
            query["crop"] = cropId
            query["from_date"] = fromDate
            query["to_date"] = endDate
            query["offset"] = 0
            itemCount = 0

            Handler().postDelayed(Runnable {
                if (CheckConnectionStatus.isInternetAvailable(context)) {
                    getATPListAPI(query)
                } else {
                    mContext.makeToast(resources.getString(R.string.no_internet_connection))
                }
            }, 500)


        }
    }


    /*
    * get All ATP list
    * */
    private fun getATPListAPI(model: HashMap<String, Any>) {
        if (itemCount != 0 || filteredData.size == 0) {
            mContext.showProgress()
        }
//        progressBar?.visibility = View.VISIBLE
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).atpList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ATPBean>() {
                override fun onCompleted() {
                    //do nothing
                }

                override fun onError(e: Throwable) {
                    mContext.hideProgress()

                    //mContext.makeToast("Network error")
                }

                override fun onNext(atpBean: ATPBean) {


                    //dataBaseHelperNew.addATPLIST(mContext,atpBean)
                    if (atpBean.status_code == 200) {
                        if (itemCount == 0) {
                            filteredData.clear()
                            dataBaseHelperNew.DelATPLISTWithoutClearing(mContext, atpBean)
                        }

                        if (atpBean.data.size > 0) {
                            filteredData.addAll(atpBean.data)

                            dataBaseHelperNew.addATPLISTWithoutClearing(mContext, atpBean)

                            if (currentPage < totalPage) {
                            } else {
                                mIsLastPage = true
                            }
                            mIsLoading = false

                            txtNoTickets.visibility = View.GONE
                            if (itemCount == 0) {
                                adapter = SupervisorATPListAdapter(
                                    filteredData,
                                    mContext,
                                    object : OnClickListener {
                                        override fun onClick(position: Int) {
                                            val query = HashMap<String, Any>()
                                            query["apiToken"] = preference.getString("token")
                                            query["plan_atp_id"] =
                                                filteredData[position].plan_atp_id

                                            if (CheckConnectionStatus.isInternetAvailable(
                                                    requireContext()
                                                )
                                            ) {
                                                addMyATPAPI(query)
                                            } else {
                                                mContext.makeToast(resources.getString(R.string.no_internet_connection))
                                            }
                                        }
                                    })
                                recyclerViewATP.layoutManager = linearLayout
                                recyclerViewATP.adapter = adapter
                            } else {
                                adapter.notifyDataSetChanged()
                            }
                            itemCount++
                        } else {
                            adapter.notifyDataSetChanged()
                            if (itemCount == 0) {
                                txtNoTickets.visibility = View.VISIBLE
                            }
                        }
                    } else {
                        if (atpBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            mContext.makeToast(atpBean.message)
                        }

                    }

                    mContext.hideProgress()
                }
            }
            )
    }


    /*
    * add to my ATp
    * */
    private fun addMyATPAPI(model: HashMap<String, Any>) {
        mContext.showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addMyAtp(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    mContext.hideProgress()
                    mContext.makeToast("Network error")
                    logEventCustom(
                        e.localizedMessage ?: "Custom Message -> error response is null",
                        "v2/addMyAtp"
                    )
                }

                override fun onNext(successBean: SuccessBean) {
                    mContext.hideProgress()
                    if (successBean.statusCode == 200) {
                        mContext.makeToast(successBean.message)
                    } else {
                        logEventCustom(
                            successBean.message,
                            "v2/addMyAtp"
                        )
                        mContext.makeToast(successBean.message)

                    }
                }
            })
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
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
        Utility.logEvent("ATLListFragment", "failed_api_$apiName", bundle)
    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
//            cropId=it.getStringExtra(Constants.CROP_ID)
//            productId=it.getStringExtra(Constants.PRODUCT_ID)
//            fromDate=it.getStringExtra(Constants.FROM_DATE)
//            endDate=it.getStringExtra(Constants.TO_DATE)
//            ddId=it.getStringExtra(Constants.PRODUCT_NAME)
//            rsmId=it.getStringExtra("ASMID")
//            asmId=it.getStringExtra("RSMID")
//            flId=it.getStringExtra("FLID")
//
//
//            val query = HashMap<String, Any>()
//            query["apiToken"] = preference.getString("token")
//            query["rsm"] =rsmId
//            query["asm"] = asmId
//            query["bdo_id"] = flId
//            query["dd_id"] = ddId
//            query["product"] = productId
//            query["crop"] = cropId
//            query["from_date"] = fromDate
//            query["to_date"] = endDate
//            if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
//                getATPListAPI(query)
//            } else {
//                mContext.makeToast(resources.getString(R.string.no_internet_connection))
//            }

        }
    }


}
