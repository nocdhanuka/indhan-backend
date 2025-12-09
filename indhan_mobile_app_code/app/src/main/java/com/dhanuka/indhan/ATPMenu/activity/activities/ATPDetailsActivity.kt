package com.dhanuka.indhan.ATPMenu.activity.activities

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.text.SpannableString
import android.text.TextUtils
import android.view.View
import android.view.Window
import android.widget.Toast
import com.dhanuka.indhan.ATPMenu.activity.ATPBean
import com.dhanuka.indhan.ATPMenu.activity.dialogs.DemoChooseDialog
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Fonts.RegularEditText
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSuccess
import com.dhanuka.indhan.util.Utility
import com.dhanuka.indhan.validatefarmers.ValidateFarmersActivity
import kotlinx.android.synthetic.main.activity_atpdetails.*
import org.joda.time.DateTime
import org.joda.time.format.DateTimeFormat
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*


class ATPDetailsActivity : BaseActivity() {
    override fun onClick(v: View?) {
    }

    lateinit var response: ATPBean.Data
    private var canBePlanned: Boolean = false
    lateinit var preference: Preference
    var canActivted: Boolean = false
    var canInvited: Boolean = false
    private var designation = ""
    var isBDO = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_atpdetails)

        preference = Preference(this)

        designation = preference.getString("designation")
        if (designation == "BDO-FL" || designation == "SALES-FL" || designation == "BD officer" || designation == "SL-FL") {
            isBDO = true
        }

        response = intent.getSerializableExtra(Constants.ATP_DETAIL) as ATPBean.Data

        canActivted = response.data_source_id.equals("Field Day", true)

        setView(response)

        txtInvitedFarmer.setOnClickListener {
            val intent = Intent(this, InviteFarmerActivity::class.java)
            intent.putExtra(Constants.PLAN_ATP_ID, response.plan_atp_id)
            intent.putExtra("KEY", tv_invited.text.toString())

            startActivityForResult(intent, 100)
        }

        txtInviteFarmerCount.setOnClickListener {
            val intent = Intent(this@ATPDetailsActivity, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", response.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        activateTicketBtn.setOnClickListener {
            if (response.status == "1" && (response.reason == "" && !isBDO)) {
                showdialog(0)
            } else if (response.status == "3" && (response.reason == "" && isBDO)) {
                showdialog(0)
            } else {
                if (!canActivted) {
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        activateDemo(response.plan_atp_id, "")
                    } else {
                        saveTicketOffline()
                    }
                } else {
                    DemoChooseDialog(this, response.plan_atp_id, object : OnSuccess {
                        override fun onClick(data: String) {
                            if (data.equals("yes")) {
                                finish()
                            } else if (data.equals("No")) {
                                showdialog()
                            }
                        }
                    }).show()
                }
            }
        }

        tv_register_farmer.setOnClickListener {
            val intent = Intent(this@ATPDetailsActivity, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Total Farmers")
            intent.putExtra("key", "total_farmer")
            intent.putExtra("atpId", response.ticket_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        tv_invited.setOnClickListener {
            val intent = Intent(this@ATPDetailsActivity, InvitedFarmerListActivity::class.java)
            intent.putExtra("atpId", response.plan_atp_id)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }

        iCross.setOnClickListener {
            finish()
        }
    }

    private fun activateTicketOffline(ticketId: String) {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(this).appDatabase

            appDatabase.atpListDao().updateTicketActivated(ticketId, true)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun saveTicketOffline() {
        Single.fromCallable {

            val datum = TicketHistoryModel.Datum()
            datum.isOffline = true
            datum.tID = response.ticket_id.toInt()
            datum.ticket = response.ticket_no
            datum.datetime = getCurrentDate("yyyy-MM-dd hh:mm:ss")
            datum.datetimeFormat = getCurrentDate("EEEE, MMM dd yyyy, hh:mm a")
            datum.description =
                "${response.data_source_id}, ${response.state}, ${response.village}, ${response.tehsil}"
            datum.totalFarmers = 0
            datum.totalValidFarmers = 0
            datum.dataSource = response.data_source_id
            datum.state = response.state
            datum.district = response.district
            datum.tehsil = response.tehsil
            datum.village = response.village
            datum.demo_request_id = ""
            if (response.product.isNotEmpty())
                datum.product1 = response.product[0].product
            datum.product2 = ""
            datum.product3 = ""
            if (response.crop.isNotEmpty())
                datum.crop1 = response.crop[0].crop
            datum.crop2 = ""
            datum.crop3 = ""
            datum.isTicketClosed = false
            datum.expectedFarmers = 0
            datum.expectedPda = 0
            datum.spName1 = ""
            datum.spNumber1 = ""
            datum.spName2 = ""
            datum.spNumber2 = ""
            datum.spName3 = ""
            datum.spNumber3 = ""
            datum.spName4 = ""
            datum.spNumber4 = ""
            datum.spName5 = ""
            datum.spNumber5 = ""
            datum.spName6 = ""
            datum.spNumber6 = ""
            datum.language = response.language_id
            datum.isTicketATP = true
            datum.user_farmer = "0"
            datum.plan_atp_id = response.plan_atp_id.toInt()
            val languageModel = DatabaseManager.getLanguageId(this, response.language_id)
//            println("ATPDetailsActivity.saveTicketOffline : ${languageModel.id}")
            datum.language_id = languageModel.id
            datum.ddName = response.dd_name
            datum.ddId = response.dd_id
            datum.uploadedFile = ""
            datum.ticketStatus = "ACTIVE"
            val appDatabase = DatabaseClient.getInstance(this).appDatabase
            val id = appDatabase.ticketHistoryDao().insert(datum)
            runOnUiThread {
                Toast.makeText(
                    this,
                    "No Internet Connection. Your Data will be synced when online",
                    Toast.LENGTH_LONG
                ).show()
                finish()
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getCurrentDate(formatDate: String): String {
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        val format = SimpleDateFormat(formatDate, Locale.ENGLISH)
        return format.format(calendar.timeInMillis)
    }

    private fun shareViaEmail(
        message: String
    ) {
        try {
            val intent = Intent(Intent.ACTION_SEND)
            intent.type = "text/html"
            intent.putExtra(Intent.EXTRA_EMAIL, arrayOf(""))
            intent.putExtra(Intent.EXTRA_SUBJECT, "Meeting Detail")
            intent.putExtra(Intent.EXTRA_TEXT, message)
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            startActivityForResult(intent, 3244)
        } catch (e: Exception) {
            makeToast("There is no email client installed.")
        }
    }

    private fun setView(model: ATPBean.Data) {
        if (isBDO) {
            txtDD.visibility = View.VISIBLE
            txtDDName.visibility = View.VISIBLE
            viewDD.visibility = View.VISIBLE
            if (model.dd_id == "0") {
                txtDDName.text = "My Self"
            } else {
                txtDDName.text = model.dd_name
            }
        } else {
            txtDD.visibility = View.GONE
            txtDDName.visibility = View.GONE
            viewDD.visibility = View.GONE
        }
        if (isBDO && model.dd_id != "0") {
            activateTicketBtn.text = "Submit"
        }

        if (!TextUtils.isEmpty(model.meeting_detail)) {
            relMeetingData.visibility = View.VISIBLE
            tvMeetingDetail.text = "Venue -> ".plus(model.meeting_detail ?: "")
            if (!TextUtils.isEmpty(model.meeting_date)) {
                tvMeetingDateTime.text = "Date Time -> ${Utility.getDate(model.meeting_date!!)}"
            } else {
                tvMeetingDateTime.text = ""
            }
            tvPresenterName.visibility = View.GONE
            tvMonitorName.visibility = View.INVISIBLE
            ivShareMeetingDetail.setOnClickListener {
                val shareData = "Meeting Detail \n\n${tvMeetingDetail.text.toString().trim()}" +
                        "\n${tvMeetingDateTime.text.toString().trim()}"
                shareViaEmail(shareData)
            }
        }

        val content1 = SpannableString(model.total_invited_farmers)

        txtInviteFarmerCount.text = model.total_invited_farmers

        txtCreatedDate.text = Utility.getDateForerrorViewNew(model.created_at)


        when {
            model.product.size == 1 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product
                tv_product1.text = "- -"
                tv_product2.text = "- -"

            }
            model.product.size == 2 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product
                tv_product1.text = model.product[1].product
                tv_product2.text = "- -"

            }
            model.product.size == 3 -> {

                tv_crop.text = model.crop[0].crop
                tv_product.text = model.product[0].product
                tv_product1.text = model.product[1].product
                tv_product2.text = model.product[2].product
            }
        }

        if (model.plan_date.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.plan_date.length > 10) {
                date.text = Utility.getDateForerrorView(model.plan_date)
                try {
                    val plan_date = Utility.getDateStrFromJoda(model.plan_date)
                    val formatter = DateTimeFormat.forPattern("yyyy-MM-dd")
                    val ticketDate = formatter.parseDateTime(plan_date)
                    val currentDate = formatter.parseDateTime(DateTime.now().toString(formatter))
                    canBePlanned = ticketDate == currentDate

                    // ticketDate.month - currentDate.month == 0 old code
                    val isValid =
                        !((ticketDate.millis - currentDate.millis) <= 0 || (ticketDate.millis - currentDate.millis) > (10 * 24 * 60 * 60 * 1000))
                    canInvited = if (ticketDate.millis > currentDate.millis) {
                        isValid
                    } else {
                        false
                    }
                } catch (e: Exception) {
                    e.printStackTrace()
                }
            } else {
                date.text = Utility.getDateForView(model.plan_date)
                try {
                    val formatter = DateTimeFormat.forPattern("yyyy-MM-dd")
                    val ticketDate = formatter.parseDateTime(model.plan_date)
                    val currentDate = formatter.parseDateTime(DateTime.now().toString(formatter))
                    canBePlanned = ticketDate == currentDate
                    val different = ticketDate.millisOfDay() == currentDate.millisOfDay()
                } catch (e: Exception) {
                    e.printStackTrace()
                }
            }
        }

        if (model.language_id.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language_id

        if (model.data_source_id.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.data_source_id

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.territory.isNullOrEmpty()) tv_territory.text = "- -"
        else tv_territory.text = model.territory

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village

        if (model.total_farmers.isNullOrEmpty()) tv_register_farmer.text = "- -"
        else tv_register_farmer.text = model.total_farmers

        if (model.total_invited_farmers.isNullOrEmpty()) tv_invited.text = "- -"
        else tv_invited.text = model.total_invited_farmers

        tv_farmer.text = model.expected_farmer.toString()

        tv_pda.text = model.expected_pda.toString()
        if (!isBDO) {
            if (model.status == "1" && canBePlanned) {
                activateTicketBtn.text = "Submit"
                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                activateTicketBtn.isEnabled = true
                activateTicketBtn.isActivated = true
            } else if (model.status == "1" || !canBePlanned) {
                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                activateTicketBtn.isEnabled = false
                activateTicketBtn.isActivated = false
            } else if (model.status == "2" || !canBePlanned) {
                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                activateTicketBtn.isEnabled = false
                activateTicketBtn.isActivated = false
            } else {
                if (designation.equals("COUNTERPART-FL", true)
                    || designation.equals("CS- FL", true)
                    || designation.equals("C-SFL", true)
                ) {
                    if (model.forwhom == "2") {
                        if (model.dd_id == "0") {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                            activateTicketBtn.isEnabled = true
                            activateTicketBtn.isActivated = true
                        } else {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                            activateTicketBtn.isEnabled = false
                            activateTicketBtn.isActivated = false
                        }
                    } else {
                        activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                        activateTicketBtn.isEnabled = false
                        activateTicketBtn.isActivated = false
                    }
                } else {
                    if (preference.getString("user_type").equals("dhanuka_doctor", true)) {
                        if (model.dd_id != "0") {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                            activateTicketBtn.isEnabled = true
                            activateTicketBtn.isActivated = true
                        } else {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                            activateTicketBtn.isEnabled = false
                            activateTicketBtn.isActivated = false
                        }
                    } else {
                        activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                        activateTicketBtn.isEnabled = false
                        activateTicketBtn.isActivated = false
                    }
                }
            }

            if (model.status != "1" && !canInvited) {
                txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.AppWhite))
                txtInvitedFarmer.isEnabled = false
                txtInvitedFarmer.isActivated = false
            } else if (canBePlanned) {
                txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.AppWhite))
                txtInvitedFarmer.isEnabled = false
                txtInvitedFarmer.isActivated = false
            } else {
                txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.colorButton))
                txtInvitedFarmer.isEnabled = true
                txtInvitedFarmer.isActivated = true
            }

        } else {
            //if user type is BDO
            if (model.dd_id != "0") {
                //not self assign
                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                activateTicketBtn.isEnabled = false
                activateTicketBtn.isActivated = false
            } else {
                //self assign
                if (model.status == "1" || !canBePlanned) {
                    activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                    activateTicketBtn.isEnabled = false
                    activateTicketBtn.isActivated = false
                } else if (model.status == "3" && model.reason == "") {
                    activateTicketBtn.text = "Submit"
                    activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                    activateTicketBtn.isEnabled = true
                    activateTicketBtn.isActivated = true
                } else if (model.status == "2") {
                    activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                    activateTicketBtn.isEnabled = false
                    activateTicketBtn.isActivated = false
                } else if (model.status == "3" && model.reason != "") {
                    activateTicketBtn.text = "Submit"
                    activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                    activateTicketBtn.isEnabled = false
                    activateTicketBtn.isActivated = false
                } else {
                    if (model.forwhom == "2") {
                        if (model.dd_id == "0") {
                            if (designation.equals("COUNTERPART-FL", true)) {
                                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                                activateTicketBtn.isEnabled = true
                                activateTicketBtn.isActivated = true
                            } else {
                                activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                                activateTicketBtn.isEnabled = false
                                activateTicketBtn.isActivated = false
                            }
                        } else {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                            activateTicketBtn.isEnabled = false
                            activateTicketBtn.isActivated = false
                        }
                    } else {
                        if (model.dd_id == "0") {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.colorButton))
                            activateTicketBtn.isEnabled = true
                            activateTicketBtn.isActivated = true
                        } else {
                            activateTicketBtn.setBackgroundColor(resources.getColor(R.color.AppWhite))
                            activateTicketBtn.isEnabled = false
                            activateTicketBtn.isActivated = false
                        }
                    }
                }

                if (model.status != "1" && !canInvited) {
                    txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.AppWhite))
                    txtInvitedFarmer.isEnabled = false
                    txtInvitedFarmer.isActivated = false
                } else if (canBePlanned) {
                    txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.AppWhite))
                    txtInvitedFarmer.isEnabled = false
                    txtInvitedFarmer.isActivated = false
                } else {
                    txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.colorButton))
                    txtInvitedFarmer.isEnabled = true
                    txtInvitedFarmer.isActivated = true
                }
            }
        }

        if (isBDO) {
            if (response.dd_id != "0") {
                txtInvitedFarmer.setBackgroundColor(resources.getColor(R.color.AppWhite))
                txtInvitedFarmer.isEnabled = false
                txtInvitedFarmer.isActivated = false
            }
        }

    }

    private fun activateDemo(planAtpId: String, demoId: String) {
        showProgress()
        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("plan_atp_id", planAtpId)
        query.put("demo_request_id", demoId)
        query.put("user_farmer", "")

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).activeDemo(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    Toast.makeText(
                        this@ATPDetailsActivity,
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    ).show()
                }

                override fun onNext(commonBean: CommonAtpResponse) {
                    hideProgress()
                    if (commonBean.status_code == 200) {
                        Toast.makeText(
                            this@ATPDetailsActivity,
                            commonBean.message,
                            Toast.LENGTH_LONG
                        ).show()
                        finish()
                    } else if (commonBean.status_code == 201) {
                        showdialog()
                    } else {
                        if (commonBean != null && !TextUtils.isEmpty(commonBean.message)) {
                            Toast.makeText(
                                this@ATPDetailsActivity,
                                commonBean.message,
                                Toast.LENGTH_LONG
                            ).show()
                        }
                    }
                }
            })
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_cancel_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        //msg.text = "You have not attended some Tickets, Please ask your FL to provide the reason"
        msg.text = getString(R.string.fl_close_previous_ticket)
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        dialog.show()
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 100) {
            tv_invited.text = data?.getStringExtra("COUNT")
            txtInviteFarmerCount.text = data?.getStringExtra("COUNT")
            updateFarmerCountInDb(tv_invited.text.toString())
        }
    }

    private fun updateFarmerCountInDb(count: String) {
        Single.fromCallable {
            val database = DatabaseClient.getInstance(this).appDatabase
            database.atpListDao().updateFarmerCount(response.plan_atp_id, count)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun showdialog(postion: Int) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_close_atp)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium
        val edtTextField = dialog.findViewById<View>(R.id.edtTextField) as RegularEditText

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            if (edtTextField.text.toString() != "") {
                val query = HashMap<String, Any>()
                query["apiToken"] = preference.getString("token")
                query["atp_id"] = response.plan_atp_id
                query["reason"] = edtTextField.text.toString()
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    reasonAPI(query)
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }

            } else {
                makeToast("Please Enter Reason for Close")
            }
            dialog.dismiss()
        }
        dialog.show()
    }

    private fun reasonAPI(model: HashMap<String, Any>) {
        showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).activateTicketReason(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast("Network error")
                }

                override fun onNext(loginBean: ParticipationPlannedResponse) {
                    hideProgress()
                    if (loginBean.statusCode == 200) {
                        makeToast(loginBean.message)
                        val query = HashMap<String, Any>()
                        query["apiToken"] = preference.getString("token")
                        if (CheckConnectionStatus.isInternetAvailable(this@ATPDetailsActivity)) {
                            finish()
                        } else {
                            makeToast(resources.getString(R.string.no_internet_connection))
                        }

                    } else {
                        makeToast(loginBean.message)

                    }
                }
            })
    }
}
