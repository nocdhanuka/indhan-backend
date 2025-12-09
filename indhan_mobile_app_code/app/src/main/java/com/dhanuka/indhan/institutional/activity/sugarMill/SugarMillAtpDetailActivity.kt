package com.dhanuka.indhan.institutional.activity.sugarMill

import android.os.Bundle
import android.view.View
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.sugarmill.SugarMillAtpListData
import com.google.gson.Gson
import kotlinx.android.synthetic.main.tea_estate_atp_detail_activity.*
import kotlin.math.roundToInt

class SugarMillAtpDetailActivity : BaseActivity() {
    private var atpData: SugarMillAtpListData? = null
    override fun onClick(v: View?) {

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.tea_estate_atp_detail_activity)
        tvLabel.text = "Sugar Mill – ATP Detail"
        ivBack.setOnClickListener {
            finish()
        }
        setData()
    }

    private fun setData() {
        intent?.getStringExtra("atp_data")?.let {
            atpData = Gson().fromJson(it, SugarMillAtpListData::class.java)
        }
        atpData?.let {
            Glide.with(this)
                .asBitmap()
                .load(Constants.IMAGEBASEURL + it.arrival_image)
                .into(ivArrivalImage)

            Glide.with(this)
                .asBitmap()
                .load(Constants.IMAGEBASEURL + it.departure_image)
                .into(ivDepartureImage)
            tvArrivalText.text = "Arrival :- ".plus(it.arrival_time)
            tvDepartureText.text = "Departure :- ".plus(it.departure_time)
            tvMeTicketNo.text = it.ticket_number
            tvMeDate.text = it.created_at

            tvMeDescription.text =
                it.sugarmill_name.plus("\n${if (it.sugarmill_office_type == "1") "\nType :- Head Office" else "\nType :- Branch Office"}")
                    .plus("\nCategory :-${it.company_category}")
            total_farmer.text = it.district
            total_valid_farmers.text = " | ".plus(it.state)
            tvArrivalDescription.text = it.arrival_comment
            tvDepartureDescription.text = it.departure_comment

            var duration = ""
            val duet = it.duration ?: "0"
            duration = if (duet.contains(".")) {
                "- ${duet.split(".")[0].toInt()} hours " +
                        "0.${duet.split(".")[1]}".toDouble().times(60)
                            .toInt() + " minutes"
            } else {
                "- ${(duet.toDouble().roundToInt())} hours"
            }

            txtActivityDuration.text = "Activity Duration : $duration"

            val isValid = it.is_validate
            if (isValid != null) {
                txtValid.visibility = View.VISIBLE
                if (isValid.equals("1", ignoreCase = true)) {
                    txtValid.setTextColor(resources.getColor(R.color.green_btn))
                } else {
                    txtValid.setTextColor(resources.getColor(R.color.red_demo))
                }
                "Is Valid Participation :- ${
                    if (isValid.equals(
                            "1",
                            ignoreCase = true
                        )
                    ) "Yes" else "No"
                }".also { it2 -> txtValid.text = it2 }
            } else {
                txtValid.visibility = View.GONE
            }
            txtValidDistance.text =
                "Participation Distance :- ${if (it.distance == null) "0 meters" else "${it.distance} meters"}"
        }
    }
}