package com.dhanuka.indhan.teamsummary.activities

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.TextView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.teamsummary.models.MonthNameModel
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_team_summary_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.DecimalFormat
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.HashMap


class TeamSummaryFilterActivity : BaseActivity() {
    var getMonth: String = ""
    lateinit var getFrom: String
    lateinit var getTo: String
    var selectedDate = ""
    private var fl = ""
    private var asmId = ""
    private var rsmId = ""
    private lateinit var preference: Preference

    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog
    private lateinit var flSpinnerDialog: FLNameDialog

    private var flList: java.util.ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: java.util.ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: java.util.ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()

    var monthList: ArrayList<MonthNameModel> = ArrayList()
    lateinit var getMonthNameDialog: GetMonthNameDialog
    private var userType = ""
    private var type = ""
    private var ID = ""
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_team_summary_filter)
        preference = Preference(this)

        //apply click on view
        applyClickOnView()
        //get month name in array list
        getMonthNameInArrayList()

        if (!preference.getString("user_type").equals("BDO", false)) {
            when {
                preference.getString("user_type").equals("SALES_REGIONAL") -> {
                    ID = preference.getString("user_id")
                    type = "sales_regional"
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llRSM.visibility = View.GONE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("SALES_AREA") -> {
                    ID = preference.getString("user_id")
                    type = "sales_area"
                    llFL.visibility = View.VISIBLE
                    llASM.visibility = View.GONE
                    llRSM.visibility = View.GONE
                    getFilterList(preference.getString("token"))
                }
                else -> {
                    llRSM.visibility = View.VISIBLE
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
            }
        } else {
            llASM.visibility = View.GONE
            llRSM.visibility = View.GONE
            llFL.visibility = View.GONE
        }


        flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtFL.text = flList[position].name
                fl = flList[position].id
                ID = sId
                type = "bdo"
            }
        })

        asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtASM.text = item
                asmId = sId
                ID = sId
                txtFL.text = ""
                type = "sales_area"
                getFilterList(preference.getString("token"))
            }
        })

        rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtRSM.text = item
                rsmId = sId
                ID = sId
                type = "sales_regional"
                txtASM.text = ""
                txtFL.text = ""
                getFilterList(preference.getString("token"))
            }
        })
    }

    /**
     * get month name in array list
     */
    private fun getMonthNameInArrayList() {

        var arr = arrayOf(
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        )
        for (i in arr) {
            monthList.add(MonthNameModel(i))
        }
    }

    /**
     * apply click on click on view
     */
    private fun applyClickOnView() {
        btnClear.setOnClickListener(this)
        btnApply.setOnClickListener(this)
        iCross.setOnClickListener(this)
        llMonth.setOnClickListener(this)
        llFrom.setOnClickListener(this)
        llTo.setOnClickListener(this)
        llFL.setOnClickListener(this)
        llASM.setOnClickListener(this)
        llRSM.setOnClickListener(this)

    }

    /**
     * function to clear form
     */
    public fun clearForm() {
        txtMonth.text = ""
        txtFrom.text = ""
        txtTo.text = ""
        txtRSM.text = ""
        txtASM.text = ""
        txtFL.text = ""

    }

    /**
     * function for clicking on view
     */
    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.llMonth -> {
                getMonthNameDialog = GetMonthNameDialog(
                    this@TeamSummaryFilterActivity,
                    monthList,
                    "Select or Search Month",
                    object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            getMonth = DecimalFormat("00").format(position + 1)
                            txtMonth.text = item
                        }

                    })
                getMonthNameDialog.showSpinerDialog()
            }

            R.id.btnClear -> {
                //clear form
                clearForm()
            }

            R.id.btnApply -> {

                if (validateForm()) {
                    // makeToast("ok")
                    showProgress()
                    var intent = Intent()
                    intent.putExtra(Constants.GET_MONTH, getMonth)
                    intent.putExtra(Constants.FROM, getFrom)
                    intent.putExtra(Constants.TO_DATE, getTo)
                    intent.putExtra("RSM", rsmId)
                    intent.putExtra("ASM", asmId)
                    intent.putExtra("FL", fl)
                    setResult(Activity.RESULT_OK, intent)
                    hideProgress()
                    this@TeamSummaryFilterActivity.finish()
                }
            }

            R.id.iCross -> {
                this@TeamSummaryFilterActivity.finish()
            }
            R.id.llFrom -> {
                showDate(txtFrom)
            }
            R.id.llTo -> {
                showDate(txtTo)
            }
            R.id.llFL -> {
                if (!flSpinnerDialog.isEnable)
                    flSpinnerDialog.showSpinerDialog()

            }
            R.id.llRSM -> {
                if (!rsmNameDialog.isEnable)
                    rsmNameDialog.showSpinerDialog()
            }
            R.id.llASM -> {
                if (!asmNameDialog.isEnable)
                    asmNameDialog.showSpinerDialog()
            }
        }
    }

    /**
     * function for validating Form
     */
    fun validateForm(): Boolean {
//        getMonth = txtMonth.text.toString()
        getFrom = txtFrom.text.toString()
        getTo = txtTo.text.toString()
//        if (getMonth.isNullOrBlank() && (getFrom.isNullOrBlank() && getTo.isNullOrBlank())) {
//            makeToast(resources.getString(R.string.please_select_either_month_date))
//            return false
//        }
        return true
    }

    /*
show date
*/
    private fun showDate(txtFromDate: TextView): Unit {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            DatePickerDialog.OnDateSetListener { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                //lblDate.setText("" + dayOfMonth + " " + MONTHS[monthOfYear] + ", " + year)

                selectedDate = "" + year + "-" + (monthOfYear + 1) + "-" + dayOfMonth
                txtFromDate.text = selectedDate

            },
            year,
            month,
            day
        )
        //  mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());
        mDatePicker.datePicker.maxDate = System.currentTimeMillis();

        // Subtract 3 months from Calendar updated date
        c.add(Calendar.MONTH, -6)
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.getDatePicker().setMinDate(c.getTimeInMillis());
        mDatePicker.show()
    }

    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
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
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
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
                        asmList.addAll(villageBean.data.aSMList)
                        flList.addAll(villageBean.data.bDOList)
//                            ddList.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }
}