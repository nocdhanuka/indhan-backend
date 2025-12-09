package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment


import android.app.Activity
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DataBaseHelperNew
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.SupervisorMyATPAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.fragment_my_at.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set

/**
 * A simple [Fragment] subclass.
 */
class MyATPFragment : Fragment() {

    private lateinit var mContext: SupervisorATPActivity
    private lateinit var preference: Preference
    private lateinit var adapter: SupervisorMyATPAdapter

    private var productId = ""
    private var cropId = ""
    private var fromDate = ""
    private var endDate = ""
    private var rsmId = ""
    private var asmId = ""
    private var flId = ""
    private var ddId = ""
    private val filteredData = ArrayList<ATPBean.Data>()
    private lateinit var dbHelperNew: DataBaseHelperNew


    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        mContext = requireContext() as SupervisorATPActivity
        preference = Preference(mContext)
        dbHelperNew = DataBaseHelperNew(mContext)
        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.fragment_my_at, container, false)
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
            if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
                getATPListAPI(query)


//            } else {
//                if(dbHelperNew.getMyATP(mContext).data!=null){
//                    filteredData.addAll(dbHelperNew.getMyATP(mContext).data)
//                    //filteredData.addAll(Gson().fromJson(preference.getString("myATP"),ATPBean::class.java).data)
//                    if(filteredData.size>0){
//                        txtNoTickets.visibility=View.GONE
//                        adapter = SupervisorMyATPAdapter(filteredData, mContext, object : OnClickListener {
//                            override fun onClick(position: Int) {
//
//                            }
//                        })
//                        recyclerViewMyatp.layoutManager = LinearLayoutManager(mContext)
//                        recyclerViewMyatp.adapter = adapter
//                    }else{
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
        mContext = requireContext() as SupervisorATPActivity
//        dbHelperNew= DataBaseHelperNew(mContext)

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
        if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            getATPListAPI(query)
            if (dbHelperNew.getMyATP(mContext).data != null) {
                filteredData.addAll(dbHelperNew.getMyATP(mContext).data)
                //filteredData.addAll(Gson().fromJson(preference.getString("myATP"),ATPBean::class.java).data)
                if (filteredData.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter =
                        SupervisorMyATPAdapter(filteredData, mContext, object : OnClickListener {
                            override fun onClick(position: Int) {

                            }
                        })
                    recyclerViewMyatp.layoutManager =
                        LinearLayoutManager(mContext)
                    recyclerViewMyatp.adapter = adapter
                } else {
                    txtNoTickets.visibility = View.VISIBLE

                }
            }
        } else {
            if (dbHelperNew.getMyATP(mContext).data != null) {
                filteredData.addAll(dbHelperNew.getMyATP(mContext).data)
                //filteredData.addAll(Gson().fromJson(preference.getString("myATP"),ATPBean::class.java).data)
                if (filteredData.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter =
                        SupervisorMyATPAdapter(filteredData, mContext, object : OnClickListener {
                            override fun onClick(position: Int) {

                            }
                        })
                    recyclerViewMyatp.layoutManager =
                        LinearLayoutManager(mContext)
                    recyclerViewMyatp.adapter = adapter
                } else {
                    txtNoTickets.visibility = View.VISIBLE

                }
            }
            mContext.makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun getATPListAPI(model: HashMap<String, Any>) {
//        mContext.showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).MyAtp(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ATPBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
//                        mContext.hideProgress()
                    //mContext.makeToast("Network error")
                }

                override fun onNext(atpBean: ATPBean) {
                    dbHelperNew.AddDataInBox(mContext, atpBean)
                    filteredData.clear()
                    filteredData.addAll(atpBean.data)
                    if (atpBean.status_code == 200) {
                        if (atpBean.data.size > 0) {
                            txtNoTickets.visibility = View.GONE
                            adapter = SupervisorMyATPAdapter(
                                filteredData,
                                mContext,
                                object : OnClickListener {
                                    override fun onClick(position: Int) {

                                    }
                                })
                            recyclerViewMyatp.layoutManager =
                                LinearLayoutManager(
                                    mContext
                                )
                            recyclerViewMyatp.adapter = adapter
                        } else {
                            adapter.notifyDataSetChanged()
                            txtNoTickets.visibility = View.VISIBLE

                        }


                    } else {
                        if (atpBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            mContext.makeToast(atpBean.message)
                        }


                    }
                }
            }
            )
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
            // if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            getATPListAPI(query)
//            } else {
//                mContext.makeToast(resources.getString(R.string.no_internet_connection))
//            }

        }
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
