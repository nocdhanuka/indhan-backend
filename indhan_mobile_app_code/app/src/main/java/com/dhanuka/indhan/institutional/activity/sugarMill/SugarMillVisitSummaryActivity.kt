package com.dhanuka.indhan.institutional.activity.sugarMill

import android.graphics.Color
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.ASMNameDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.FLNameDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.RSMNameDialog
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillVisitSummaryData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillVisitSummaryResponse
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_tea_estate_visit_summary.*
import org.eazegraph.lib.models.PieModel
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class SugarMillVisitSummaryActivity : BaseActivity() {

    private var flList: java.util.ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: java.util.ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: java.util.ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()
    private var asmNameDialog: ASMNameDialog? = null
    private var rsmNameDialog: RSMNameDialog? = null
    private var flSpinnerDialog: FLNameDialog? = null
    private var preference: Preference? = null
    private var year: String = ""
    private var month: String = ""
    private var yearSpinner: NumberSpinner? = null
    private var monthSpinner: NumberSpinner? = null
    private var fl = ""
    private var asmId = ""
    private var rsmId = ""
    private var type = ""
    private var ID = ""


    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnReset -> {
                year = ""
                month = ""
                txtASM.text = ""
                txtRSM.text = ""
                txtFL.text = ""
                fl = ""
                asmId = ""
                rsmId = ""
                type = ""
                ID = preference?.getString("user_id") ?: ""
                type = (preference?.getString("user_type") ?: "").lowercase()
                txtFilterCrop.text = ""
                txtFilterProduct.text = ""
                visitSummaryData()
            }

            R.id.txtFilterProduct -> {
                yearSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }

            R.id.txtFilterCrop -> {
                monthSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }

            R.id.ivBack -> {
                finish()
            }

            R.id.llViewPlannedVisit -> {
                launchIntent(SugarMillAtpListActivity::class.java, false)
            }

            R.id.tvPlanVisitA -> {
                val bundle = Bundle()
                bundle.putString("from", "A")
                launchIntent(SugarMillPlanNewAtpActivity::class.java, bundle, false)
            }

            R.id.tvPlanVisitB -> {
                val bundle = Bundle()
                bundle.putString("from", "B")
                launchIntent(SugarMillPlanNewAtpActivity::class.java, bundle, false)
            }

            R.id.tvPlanVisitC -> {
                val bundle = Bundle()
                bundle.putString("from", "C")
                launchIntent(SugarMillPlanNewAtpActivity::class.java, bundle, false)
            }
            R.id.llFL -> {
                flSpinnerDialog?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.llRSM -> {
                rsmNameDialog?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.llASM -> {
                asmNameDialog?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_tea_estate_visit_summary)
        tvLabelName.text = "Sugar Mill - Visits"
        preference = Preference(this)
        setFilterData()
        clickListeners()
        setDropDownData()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            visitSummaryData()
        } else {
            preference?.getString("sugar_visit")?.let {
                setData(Gson().fromJson(it, SugarMillVisitSummaryResponse::class.java)?.data)
            }
            makeToast("Internet connection not available.")
        }
    }

    private fun setFilterData() {
        if (!preference?.getString("user_type").equals("BDO", false)) {
            when {
                preference?.getString("user_type").equals("SALES_REGIONAL") -> {
                    ID = preference?.getString("user_id")!!
                    type = "sales_regional"
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        getFilterList(preference?.getString("token"))
                    }
                }
                preference?.getString("user_type").equals("SALES_AREA") -> {
                    ID = preference?.getString("user_id")!!
                    type = "sales_area"
                    llFL.visibility = View.VISIBLE
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        getFilterList(preference?.getString("token"))
                    }
                }
                else -> {
                    llRSM.visibility = View.VISIBLE
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        getFilterList(preference?.getString("token"))
                    }
                }
            }
            flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
                override fun onClick(item: String, position: Int, sId: String) {
                    txtFL.text = flList[position].name
                    fl = flList[position].id
                    ID = sId
                    type = "bdo"
                    if (CheckConnectionStatus.isInternetAvailable(this@SugarMillVisitSummaryActivity)) {
                        visitSummaryData()
                    }
                }
            })

            asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
                override fun onClick(item: String, position: Int, sId: String) {
                    txtASM.text = item
                    asmId = sId
                    ID = sId
                    type = "sales_area"
                    flList.clear()
                    txtFL.text = ""
                    fl = ""
                    if (CheckConnectionStatus.isInternetAvailable(this@SugarMillVisitSummaryActivity)) {
                        visitSummaryData()
                        getFilterList(preference?.getString("token"))
                    }
                }
            })

            rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
                override fun onClick(item: String, position: Int, sId: String) {
                    txtRSM.text = item
                    rsmId = sId
                    ID = sId
                    type = "sales_regional"
                    asmList.clear()
                    flList.clear()
                    txtASM.text = ""
                    txtFL.text = ""
                    asmId = ""
                    fl = ""
                    if (CheckConnectionStatus.isInternetAvailable(this@SugarMillVisitSummaryActivity)) {
                        visitSummaryData()
                        getFilterList(preference?.getString("token"))
                    }
                }
            })
        }

    }

    private fun getFilterList(token: String?) {
        showProgress()
        val query = java.util.HashMap<String, Any>()
        query["type"] = type
        query["apiToken"] = token ?: ""
        query["id"] = ID

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).departmentlist(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ACPFIlterBean>() {
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

                override fun onNext(villageBean: ACPFIlterBean) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        rsmList.addAll(villageBean.data.rSMList)
                        asmList.addAll(villageBean.data.aSMList)
                        flList.addAll(villageBean.data.bDOList)
                    }
                }
            })
    }

    private fun setDropDownData() {
        val arrayListMonth = ArrayList<String>()
        val arrayListYear = ArrayList<String>()
        val initialYear = 2021

        for (i in 0..11) {
            arrayListMonth.add("${i + 1}")
        }

        for (i in 0..50) {
            arrayListYear.add("${initialYear + i}")
        }

        monthSpinner =
            NumberSpinner(this@SugarMillVisitSummaryActivity, arrayListMonth, "Select Month")
        monthSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                month = item
                txtFilterCrop.text = "Month ".plus(item)
                visitSummaryData()
            }
        })

        yearSpinner =
            NumberSpinner(this@SugarMillVisitSummaryActivity, arrayListYear, "Select Year")
        yearSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                year = item
                txtFilterProduct.text = "Year ".plus(item)
                visitSummaryData()
            }
        })
    }

    private fun visitSummaryData() {
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = token
        if (!TextUtils.isEmpty(year)) {
            query["year"] = year
        }
        if (!TextUtils.isEmpty(month)) {
            query["month"] = month
        }
        if (!TextUtils.isEmpty(ID)) {
            query["userId"] = ID
        }
        if (!TextUtils.isEmpty(type)) {
            query["userType"] = type
        }
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).visitSummarySugarMill(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SugarMillVisitSummaryResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    hideProgress()
                }

                override fun onNext(it: SugarMillVisitSummaryResponse?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            preference?.putString("sugar_visit", Gson().toJson(it))
                            setData(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    //target - red //FFBA68C8
    //plan  -orange //FFFF8A65
    //visit -green //FF81C784

    private fun setData(data: SugarMillVisitSummaryData?) {

        data?.let {
            //Category A
            pieChartA.clearChart()
            pieChartA.clearAnimation()

            pieChartA.addPieSlice(
                PieModel(
                    "Completed",
                    Integer.parseInt(it.cat_a_target.toString()).toFloat(),
                    Color.parseColor("#BA68C8")
                )
            )

            pieChartA.addPieSlice(
                PieModel(
                    "Not Completed 2",
                    Integer.parseInt(it.cat_a_plan.toString())
                        .toFloat(),
                    Color.parseColor("#FF8A65")
                )
            )

            pieChartA.addPieSlice(
                PieModel(
                    "Not Completed",
                    Integer.parseInt(it.cat_a_visit.toString())
                        .toFloat(),
                    Color.parseColor("#81C784")
                )
            )



            tvCategoryDataA.text =
                "Target - ${it.cat_a_target}\nPlan - ${it.cat_a_plan}\nVisit - ${it.cat_a_visit}"
            pieChartA.startAnimation()

            //Category B
            pieChartB.clearChart()
            pieChartB.clearAnimation()
            pieChartB.addPieSlice(
                PieModel(
                    "Completed",
                    Integer.parseInt(it.cat_b_target.toString()).toFloat(),
                    Color.parseColor("#BA68C8")
                )
            )

            pieChartB.addPieSlice(
                PieModel(
                    "Not Completed",
                    Integer.parseInt(it.cat_b_plan.toString())
                        .toFloat(),
                    Color.parseColor("#FF8A65")
                )
            )

            pieChartB.addPieSlice(
                PieModel(
                    "Not Completed",
                    Integer.parseInt(it.cat_b_visit.toString())
                        .toFloat(),
                    Color.parseColor("#81C784")
                )
            )

            tvCategoryDataB.text =
                "Target - ${it.cat_b_target}\nPlan - ${it.cat_b_plan}\nVisit - ${it.cat_b_visit}"
            pieChartB.startAnimation()

            //Category C
            pieChartC.clearChart()
            pieChartC.clearAnimation()
            pieChartC.addPieSlice(
                PieModel(
                    "Completed",
                    Integer.parseInt(it.cat_c_target.toString()).toFloat(),
                    Color.parseColor("#BA68C8")
                )
            )

            pieChartC.addPieSlice(
                PieModel(
                    "Not Completed",
                    Integer.parseInt(it.cat_c_plan.toString())
                        .toFloat(),
                    Color.parseColor("#FF8A65")
                )
            )

            pieChartC.addPieSlice(
                PieModel(
                    "Not Completed",
                    Integer.parseInt(it.cat_c_visit.toString())
                        .toFloat(),
                    Color.parseColor("#81C784")
                )
            )


            tvCategoryDataC.text =
                "Target - ${it.cat_c_target}\nPlan - ${it.cat_c_plan}\nVisit - ${it.cat_c_visit}"
            pieChartC.startAnimation()
        }

    }

    private fun clickListeners() {
        tvPlanVisitC.setOnClickListener(this)
        tvPlanVisitA.setOnClickListener(this)
        tvPlanVisitB.setOnClickListener(this)
        llViewPlannedVisit.setOnClickListener(this)
        btnReset.setOnClickListener(this)
        txtFilterProduct.setOnClickListener(this)
        txtFilterCrop.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        llRSM.setOnClickListener(this)
        llASM.setOnClickListener(this)
        llFL.setOnClickListener(this)
    }
}