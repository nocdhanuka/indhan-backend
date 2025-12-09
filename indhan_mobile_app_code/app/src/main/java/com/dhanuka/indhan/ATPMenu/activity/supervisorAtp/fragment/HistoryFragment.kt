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
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPActivity
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorTicketHistoryAdapter
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryAtpResponse
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel.Datum
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DataBaseHelperNew
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.fragment_history.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * A simple [Fragment] subclass.
 */
class HistoryFragment : Fragment() {

    private lateinit var mContext: SupervisorATPActivity
    private lateinit var preference: Preference
    private lateinit var adapter: SupervisorTicketHistoryAdapter
    private val ticketRecordResponseModels = ArrayList<TicketHistoryAtpResponse.Data>()

    private var productId = ""
    private var cropId = ""
    private var fromDate = ""
    private var endDate = ""
    private var rsmId = ""
    private var asmId = ""
    private var flId = ""
    private var ddId = ""
    private lateinit var filteredData: List<Datum>
    private lateinit var dbManager: DataBaseHelperNew

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {

        mContext = requireContext() as SupervisorATPActivity
        preference = Preference(mContext)
        dbManager = DataBaseHelperNew(mContext)

        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.fragment_history, container, false)
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)

        LocalBroadcastManager.getInstance(mContext)
            .registerReceiver(mMessageReceiver, IntentFilter("custom-event-name"))

        if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            getACPList()
            if (dbManager.getATPHistory(requireContext()).data != null) {
                if (dbManager.getATPHistory(requireContext()).data != null)
                    ticketRecordResponseModels.addAll(dbManager.getATPHistory(requireContext()).data!!)
                if (ticketRecordResponseModels.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter = SupervisorTicketHistoryAdapter(ticketRecordResponseModels, mContext)
                    recyclerViewHistory.layoutManager =
                        LinearLayoutManager(
                            requireContext()
                        )
                    recyclerViewHistory.adapter = adapter
                } else {
                    txtNoTickets.visibility = View.VISIBLE
                }
            }
        } else {
            if (dbManager.getATPHistory(requireContext()).data != null) {
                if (dbManager.getATPHistory(requireContext()).data != null)
                    ticketRecordResponseModels.addAll(dbManager.getATPHistory(requireContext()).data!!)
                if (ticketRecordResponseModels.size > 0) {
                    txtNoTickets.visibility = View.GONE
                    adapter = SupervisorTicketHistoryAdapter(ticketRecordResponseModels, mContext)
                    recyclerViewHistory.layoutManager =
                        LinearLayoutManager(
                            requireContext()
                        )
                    recyclerViewHistory.adapter = adapter
                } else {
                    txtNoTickets.visibility = View.VISIBLE
                }
            }
            mContext.makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        swipeLayout.setOnRefreshListener {
            if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
                getACPList()
            }
            swipeLayout.isRefreshing = false
        }
    }

    private fun getACPList() {
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


        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .SupervisorTicketHistory(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TicketHistoryAtpResponse>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                }

                override fun onNext(ticketListBean: TicketHistoryAtpResponse) {
                    dbManager.AddDataInBox(requireContext(), ticketListBean)
                    if (ticketListBean.status_code == 200) {
                        ticketRecordResponseModels.clear()
                        ticketListBean.data?.let { ticketRecordResponseModels.addAll(it) }
                        if ((ticketListBean.data?.size ?: 0) > 0) {
                            txtNoTickets.visibility = View.GONE
                            adapter =
                                SupervisorTicketHistoryAdapter(ticketRecordResponseModels, mContext)
                            recyclerViewHistory.layoutManager =
                                LinearLayoutManager(
                                    requireContext()
                                )
                            recyclerViewHistory.adapter = adapter
                        } else {
                            adapter.notifyDataSetChanged()
                            txtNoTickets.visibility = View.VISIBLE

                        }
                    } else {
                        if (ticketListBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            mContext.makeToast(ticketListBean.message)
                        }


                    }
                }
            })

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
                getACPList()

            }

        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
            data?.let {
                cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
                productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                endDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                ddId = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
                rsmId = it.getStringExtra("ASMID") ?: ""
                asmId = it.getStringExtra("RSMID") ?: ""
                flId = it.getStringExtra("FLID") ?: ""
            }
        }
    }


}
