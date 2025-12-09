package com.dhanuka.indhan.teamsummary.activities

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.TextView
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.ASMNameDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.FLNameDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.RSMNameDialog
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.SelectDdAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.teamsummary.models.TeamSummaryDdDetail
import com.dhanuka.indhan.util.OnGetId
import kotlinx.android.synthetic.main.activity_upcoming_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList

class TeamSummaryUpcomingFilterActivity : BaseActivity() {
    var selectedDate = ""
    private lateinit var preference: Preference

    private var flList: java.util.ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: java.util.ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: java.util.ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()

    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog
    private lateinit var flSpinnerDialog: FLNameDialog

    lateinit var ddDetailList: ArrayList<TeamSummaryDdDetail>
    var checkUserId: String = ""
    private var fl = ""
    private var asmId = ""
    private var rsmId = ""
    private var type = ""
    private var ID = ""

    var list: ArrayList<String> = ArrayList()
    lateinit var adapter: SelectDdAdapter
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_upcoming_filter)
        preference = Preference(this)

//        if (preference.getString("user_type").equals("BDO", false)) {
//            llASM.visibility=View.GONE
//            llRSM.visibility=View.GONE
//            llFL.visibility=View.GONE
//        } else {
//            getFilterList(preference.getString("token"))
//
//        }

        if (!preference.getString("user_type").equals("BDO", false)) {
            if (preference.getString("user_type").equals("SALES_REGIONAL")) {
//            llRSM.visibility=View.VISIBLE
                ID = preference.getString("user_id")
                type = "sales_regional"
                llASM.visibility = View.VISIBLE
                llFL.visibility = View.VISIBLE
                llRSM.visibility = View.GONE

//                llDD.visibility = View.GONE
//                frameDD.visibility = View.GONE
                getFilterList(preference.getString("token"))
            } else if (preference.getString("user_type").equals("SALES_AREA")) {
                ID = preference.getString("user_id")
                type = "sales_area"
                llFL.visibility = View.VISIBLE
                llASM.visibility = View.GONE
                llRSM.visibility = View.GONE
//                llDD.visibility = View.VISIBLE
//                frameDD.visibility = View.GONE
                getFilterList(preference.getString("token"))
            } else {
                llRSM.visibility = View.VISIBLE
                llASM.visibility = View.VISIBLE
                llFL.visibility = View.VISIBLE
//                llDD.visibility = View.VISIBLE
//                frameDD.visibility = View.GONE
                getFilterList(preference.getString("token"))
            }
        } else {
            llASM.visibility = View.GONE
            llRSM.visibility = View.GONE
            llFL.visibility = View.GONE
        }

        //apply click on view
        applyClickOnView()
        //get dd list
        if (intent.hasExtra(Constants.DDLIST)) {
            ddDetailList =
                intent.getSerializableExtra(Constants.DDLIST) as ArrayList<TeamSummaryDdDetail>
            setDemoFilterAdapter(ddDetailList)
        }



        flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtFL.text = flList[position].name
                fl = flList[position].id
                ID = sId
                type = "bdo"
                getFilterList(preference.getString("token"))
            }

        })



        asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtASM.text = item
                asmId = sId
                ID = sId
                type = "sales_area"
                txtFL.text = ""
                getFilterList(preference.getString("token"))

            }

        })

        rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtRSM.text = item
                rsmId = sId
                type = "sales_regional"
                ID = sId
                txtASM.text = ""
                txtFL.text = ""
                getFilterList(preference.getString("token"))


            }

        })
        llRSM.setOnClickListener {
            rsmNameDialog.showSpinerDialog()
        }

        llASM.setOnClickListener {
            asmNameDialog.showSpinerDialog()
        }

        llFL.setOnClickListener {
            flSpinnerDialog.showSpinerDialog()
        }


    }

    private fun setDemoFilterAdapter(list: ArrayList<TeamSummaryDdDetail>) {
        rvDdName.layoutManager =
            LinearLayoutManager(this@TeamSummaryUpcomingFilterActivity)
        adapter = SelectDdAdapter(this@TeamSummaryUpcomingFilterActivity, list)
        rvDdName.adapter = adapter
    }

    /**
     * apply click on click on view
     */
    private fun applyClickOnView() {
        btnClear.setOnClickListener(this)
        btnApply.setOnClickListener(this)
        iCross.setOnClickListener(this)
        llSelectDate.setOnClickListener(this)
        llOriginalDd.setOnClickListener(this)
        llDdWithRecycler.setOnClickListener(this)
    }

    /**
     * function to clear form
     */
    public fun clearForm() {
        if (::ddDetailList.isInitialized) {
            for (item in 0..ddDetailList.size - 1) {
                ddDetailList.get(item).isChecked = false
            }
        }
        if (::adapter.isInitialized)
            adapter.notifyDataSetChanged()
        txtDdName.text = ""
        txtSelectDate.text = ""
    }

    /**
     * function for clicking on view
     */
    override fun onClick(v: View?) {
        when (v?.id) {

            R.id.btnClear -> {
                //clear form
                clearForm()
            }

            R.id.btnApply -> {
                showProgress()
                list.clear()
                if (::ddDetailList.isInitialized) {
                    for (item in 0..ddDetailList.size - 1) {
                        if (ddDetailList.get(item).isChecked) {
                            list.add(ddDetailList.get(item).dd_id)
                        }
                    }
                    for (item in 0..list.size - 1) {
                        if (item == 0) {
                            checkUserId = ddDetailList.get(item).dd_id
                        } else {
                            checkUserId = checkUserId + "," + ddDetailList[item].dd_id
                        }

                    }
                }
                var intent = Intent()
                intent.putExtra(Constants.CHECKED_USER_ID, checkUserId)
                intent.putExtra(Constants.PLANED_DATE, selectedDate)
                intent.putExtra("RSM", rsmId)
                intent.putExtra("ASM", asmId)
                intent.putExtra("FL", fl)
                setResult(Activity.RESULT_OK, intent)
                this@TeamSummaryUpcomingFilterActivity.finish()

            }

            R.id.iCross -> {
                this@TeamSummaryUpcomingFilterActivity.finish()
            }
            R.id.llSelectDate -> {
                showDate(txtSelectDate)
            }
            R.id.llOriginalDd -> {
                llOriginalDd.visibility = View.GONE
                llDdWithRecycler.visibility = View.VISIBLE
            }
            R.id.llDdWithRecycler -> {
                llOriginalDd.visibility = View.VISIBLE
                llDdWithRecycler.visibility = View.GONE
            }
//            R.id.llRSM->{
//                rsmNameDialog.showSpinerDialog()
//            }
//            R.id.llASM->{
//                asmNameDialog.showSpinerDialog()
//
//            }
//            R.id.llFL->{
//                flSpinnerDialog.showSpinerDialog()
//
//            }

        }
    }

    /*
show date
*/
    private fun showDate(txtFromDate: TextView): Unit {
        val c = Calendar.getInstance( Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)
        var m = ""
        var d = ""

        val mDatePicker = DatePickerDialog(
            activity,
            DatePickerDialog.OnDateSetListener { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                //lblDate.setText("" + dayOfMonth + " " + MONTHS[monthOfYear] + ", " + year)
                if ((monthOfYear + 1) < 10) {
                    m = "0" + (monthOfYear + 1)
                } else {
                    m = "" + (monthOfYear + 1)
                }
                if (dayOfMonth < 10) {
                    d = "0" + dayOfMonth
                } else {
                    d = "" + dayOfMonth
                }

                selectedDate = "" + year + "-" + m + "-" + d
                txtFromDate.text = selectedDate

            },
            year,
            month,
            day
        )
        //  mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());
        mDatePicker.datePicker.minDate = System.currentTimeMillis()

        // Subtract 3 months from Calendar updated date
//        c.add(Calendar.MONTH, -3)
        // Set the Calendar new date as minimum date of date picker
//        mDatePicker.datePicker.minDate = c.timeInMillis
        mDatePicker.show()
    }

    /**
     * function for getting team summary Api
     */
    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        // query.put("ECN", ecn)
        query["type"] = type
        query["apiToken"] = token
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
                    makeToast(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: ACPFIlterBean) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        if (villageBean.data.rSMList.isEmpty()) {

                        } else {
                            rsmList.clear()

                        }
                        if (villageBean.data.aSMList.isEmpty()) {

                        } else {
                            asmList.clear()

                        }
                        if (villageBean.data.bDOList.isEmpty()) {

                        } else {
                            flList.clear()

                        }
                        rsmList.addAll(villageBean.data.rSMList)
                        rsmNameDialog.updatelist(rsmList)
                        asmList.addAll(villageBean.data.aSMList)
                        asmNameDialog.updatelist(asmList)
                        flList.addAll(villageBean.data.bDOList)
                        flSpinnerDialog.updatelist(flList)

//                            ddList.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }
}