package com.dhanuka.indhan.DemoRequestEvaluated.fragments

import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoUtilizationSummaryActivity
import com.dhanuka.indhan.DemoRequestEvaluated.models.ASMData
import com.dhanuka.indhan.DemoRequestEvaluated.models.OverViewData
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationOverview
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.fragment_demo_summary.*
import org.eazegraph.lib.models.PieModel
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set

class OverviewFragment : Fragment(), View.OnClickListener {
    private var asmName: String = ""
    private lateinit var preference: Preference
    private lateinit var mContext: DemoUtilizationSummaryActivity
    private var userType: String = ""
    private var arrayListAsm: ArrayList<ASMData> = ArrayList()
    private var stringListAsm: ArrayList<String> = ArrayList()
    private var rsmEcn: String = ""
    private var rsmListSpinner: NumberSpinner? = null

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        mContext = requireContext() as DemoUtilizationSummaryActivity
        preference = Preference(mContext)
        return inflater.inflate(R.layout.fragment_demo_summary, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        userType = mContext.userType
        txtAsmFilter.setOnClickListener(this)
        if (userType.equals("sales_regional", ignoreCase = true)) {
            txtAsmFilter.visibility = View.VISIBLE
        } else {
            txtAsmFilter.visibility = View.GONE
        }
    }

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        callApiGetData(rsmEcn)
    }

    private fun setAsmData() {
        arrayListAsm = mContext.arrayListAsm
        stringListAsm.clear()
        arrayListAsm.forEach() {
            stringListAsm.add(it.name)
        }
        rsmListSpinner = NumberSpinner(mContext, stringListAsm, "Select ASM")
        rsmListSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtAsmFilter.text = item
                asmName = item
                val asmEcn = arrayListAsm[position].ECN
                callApiGetData(asmEcn)
            }
        })
    }

    private fun callApiGetData(rsmEcn: String) {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["asm_ecn"] = rsmEcn
        if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).getUtilizationOverView(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UtilizationOverview>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable) {
                    }

                    override fun onNext(atpBean: UtilizationOverview) {
                        if (atpBean.status_code == 200) {
                            setOverViewData(atpBean.data)
                        }
                        setAsmData()
                    }
                })
        } else {
            mContext.makeToast("Internet connection not available.")
        }
    }

    private fun setOverViewData(data: OverViewData) {
        piechart.clearChart()
        piechart.clearAnimation()

        piechart2.clearChart()
        piechart2.clearAnimation()

        piechart3.clearChart()
        piechart3.clearAnimation()

        piechart4.clearChart()
        piechart4.clearAnimation()

        tvRsmTotalValue.text = "Overall Team\nRequest - ${data.totalRequest}"
        tvAdminValue.text = "Overall Team\nRequest - ${data.totalRequest}"
        tvDispatchValue.text = "Overall Team\nRequest - ${data.totalRequest}"
        tvReceivedValue.text = "Overall Team\nRequest - ${data.totalRequest}"

        //RSM
        piechart.addPieSlice(
            PieModel(
                "Rsm",
                Integer.parseInt(data.total_rsm_approval.toString()).toFloat(),
                Color.parseColor("#0CA160")
            )
        )

        val totalremaing =
            if (data.total_rsm_approval == 0) 0 else data.totalRequest - data.total_rsm_approval

        piechart.addPieSlice(
            PieModel(
                "RsmRemaining",
                Integer.parseInt(totalremaing.toString())
                    .toFloat(),
                Color.parseColor("#EF5350")
            )
        )

        piechart.startAnimation()

        //Admin
        piechart2.addPieSlice(
            PieModel(
                "Admin",
                Integer.parseInt(data.total_admin_approval.toString()).toFloat(),
                Color.parseColor("#0CA160")
            )
        )
        val totalremaing2 =
            if (data.total_admin_approval == 0) 0 else data.totalRequest - data.total_admin_approval

        piechart2.addPieSlice(
            PieModel(
                "AdminRemaining",
                Integer.parseInt(totalremaing2.toString())
                    .toFloat(),
                Color.parseColor("#EF5350")
            )
        )

        piechart2.startAnimation()

        //Dispatched
        piechart3.addPieSlice(
            PieModel(
                "Dispatched",
                Integer.parseInt(data.total_dispatch.toString()).toFloat(),
                Color.parseColor("#0CA160")
            )
        )
        val totalremaing3 =
            if (data.total_dispatch == 0) 0 else data.totalRequest - data.total_dispatch
        piechart3.addPieSlice(
            PieModel(
                "DispatchedRemaining",
                Integer.parseInt(totalremaing3.toString())
                    .toFloat(),
                Color.parseColor("#EF5350")
            )
        )

        piechart3.startAnimation()

        //Received
        piechart4.addPieSlice(
            PieModel(
                "Received",
                Integer.parseInt(data.total_received.toString()).toFloat(),
                Color.parseColor("#0CA160")
            )
        )
        val totalremaing4 =
            if (data.total_received == 0) 0 else data.totalRequest - data.total_received
        piechart4.addPieSlice(
            PieModel(
                "ReceivedRemaining",
                Integer.parseInt(totalremaing4.toString())
                    .toFloat(),
                Color.parseColor("#EF5350")
            )
        )

        piechart4.startAnimation()
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtAsmFilter -> {
                rsmListSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
        }
    }

}