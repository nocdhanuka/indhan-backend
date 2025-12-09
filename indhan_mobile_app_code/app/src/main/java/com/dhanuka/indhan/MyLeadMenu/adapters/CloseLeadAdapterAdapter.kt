package com.dhanuka.indhan.MyLeadMenu.adapters

import android.content.Context
import android.content.Intent
import android.net.Uri
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.activities.leaddetails.LeadDetailsActivity
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadData
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_closed_leads.view.*
import java.io.Serializable
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class CloseLeadAdapterAdapter(context: Context, closeLeadList: ArrayList<OpenLeadData>) :
    RecyclerView.Adapter<CloseLeadAdapterAdapter.ViewHolder>() {
    var context: Context
    var closeLeadList: ArrayList<OpenLeadData>

    init {
        this.context = context
        this.closeLeadList = closeLeadList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_closed_leads, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return closeLeadList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.view.llCloseLeadDetail.setOnClickListener {
            var showLeadDetail = Intent(context, LeadDetailsActivity::class.java)
            showLeadDetail.putExtra(
                Constants.LESD_DETAIL_DATA,
                closeLeadList.get(position) as Serializable
            )
            showLeadDetail.putExtra(Constants.FROM, "close")
            context.startActivity(showLeadDetail)
        }
        holder.view.txtLeadId.text = "Lead ID: " + closeLeadList.get(position).lead_code

        holder.view.txtLeadName.text = closeLeadList.get(position).leadName
        holder.view.txtVillageName.text = closeLeadList.get(position).villageName
        if (closeLeadList.get(position).purchased_area.equals("")) {
            holder.view.txtPurchaseAcres.text = context.resources.getString(R.string.purchased_acr)
        } else {
            holder.view.txtPurchaseAcres.text =
                context.resources.getString(R.string.purchased_acr) + " : " + closeLeadList.get(
                    position
                ).purchased_area
        }

        if (closeLeadList.get(position).call_status.equals("0")) {
            holder.view.imgCall.setImageResource(R.drawable.call_ylw)
            holder.view.txtCall.setTextColor(context.resources.getColor(R.color.yello))
        } else {
            holder.view.imgCall.setImageResource(R.drawable.call_green)
        }

        holder.view.llCall.setOnClickListener {
            if (closeLeadList.get(position).call_status.equals("0")) {
                if (closeLeadList.get(position).contact_number != null && !closeLeadList.get(
                        position
                    ).contact_number.equals("")
                ) {
                    var intent = Intent(Intent.ACTION_DIAL)
                    intent.data = Uri.parse("tel:" + closeLeadList.get(position).contact_number)
                    context.startActivity(intent)
                }
            } else {
                if (closeLeadList.get(position).contact_number != null && !closeLeadList.get(
                        position
                    ).contact_number.equals("")
                ) {
                    var intent = Intent(Intent.ACTION_DIAL)
                    intent.data = Uri.parse("tel:" + closeLeadList.get(position).contact_number)
                    context.startActivity(intent)
                }
                // Toast.makeText(context, "You have already called!", Toast.LENGTH_LONG).show()
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
}