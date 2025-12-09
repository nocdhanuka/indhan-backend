package com.dhanuka.indhan.MyLeadMenu.adapters

import android.content.Context
import android.content.Intent
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.activities.leaddetails.LeadDetailsActivity
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadData
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.TimeStamp
import com.dhanuka.indhan.util.UpdateCallListener
import kotlinx.android.synthetic.main.item_open_leads.view.*
import java.io.Serializable
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*


class OpenLeadAdapterAdapter(
    context: Context,
    openLeadList: ArrayList<OpenLeadData>,
    apiToken: String,
    onSuccess: UpdateCallListener
) : RecyclerView.Adapter<OpenLeadAdapterAdapter.ViewHolder>() {
    var context: Context
    var openLeadList: ArrayList<OpenLeadData>
    var apiToken: String
    var onSuccess: UpdateCallListener

    init {
        this.context = context
        this.openLeadList = openLeadList
        this.apiToken = apiToken
        this.onSuccess = onSuccess
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_open_leads, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return openLeadList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.llOplenLeadDetail.setOnClickListener {
            var showLeadDetail = Intent(context, LeadDetailsActivity::class.java)
            showLeadDetail.putExtra(
                Constants.LESD_DETAIL_DATA,
                openLeadList.get(position) as Serializable
            )
            showLeadDetail.putExtra(Constants.FROM, "open")
            context.startActivity(showLeadDetail)
        }
        holder.view.txtLeadId.text = "Lead ID: " + openLeadList.get(position).lead_code
        holder.view.txtLeadName.text = openLeadList.get(position).leadName
        holder.view.txtVillageName.text = openLeadList.get(position).villageName
        holder.view.txtPurchaseAcres.text =
            context.resources.getString(R.string.expected_date_of_purchase) + " : " + getTime(
                openLeadList.get(position).expected_purchase_date
            )

        //get difference between two date
        var currentDate = getCurrentDate()
        if (!openLeadList.get(position).expected_purchase_date.equals("0000-00-00")) {
            val simpleDateFormat = SimpleDateFormat("yyyy-MM-dd")
            val date1 = simpleDateFormat.parse(currentDate)
            val date2 = simpleDateFormat.parse(openLeadList.get(position).expected_purchase_date)
            var diff: Long = TimeStamp.printDifference(date1, date2)

            if (diff >= 0 && diff <= 2) {
                holder.view.imgHot.visibility = View.VISIBLE
                holder.view.txtHot.visibility = View.VISIBLE
            } else {
                holder.view.imgHot.visibility = View.GONE
                holder.view.txtHot.visibility = View.GONE
            }

        } else {
            holder.view.imgHot.visibility = View.GONE
            holder.view.txtHot.visibility = View.GONE

        }
        if (openLeadList.get(position).call_status.equals("0")) {
            holder.view.imgCall.setImageResource(R.drawable.call_ylw)
        } else {
            holder.view.imgCall.setImageResource(R.drawable.call_green)
        }


        holder.view.imgCall.setOnClickListener {
            if (openLeadList.get(position).call_status.equals("0")) {
                onSuccess.onSuccess(
                    "yes",
                    openLeadList.get(position).farmer_id,
                    openLeadList.get(position).farmer_type,
                    openLeadList.get(position).contact_number
                )
            } else {
                onSuccess.onSuccess(
                    "no",
                    openLeadList.get(position).farmer_id,
                    openLeadList.get(position).farmer_type,
                    openLeadList.get(position).contact_number
                )
                // Toast.makeText(context,"You have already called!",Toast.LENGTH_LONG).show()
            }
        }
    }


    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd")
        try {
            date1 = sdf.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }

        val timeStr: String
        val inputFormat = SimpleDateFormat("dd-MMM-yyyy")
        timeStr = inputFormat.format(date1)
        return timeStr
    }

    internal fun getCurrentDate(): String {
        val c = Calendar.getInstance( Locale.ENGLISH).time
        println("Current time => $c")

        val df = SimpleDateFormat("yyyy-MM-dd")
        val formattedDate = df.format(c)
        return formattedDate
    }
}