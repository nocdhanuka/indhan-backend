package com.dhanuka.indhan.MyLeadMenu.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.MyLeadMenu.activities.leaddetails.LeadDetailsBean
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_related_lead.view.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class RelatedLeadAdapter(
    context: Context,
    closeLeadList: ArrayList<LeadDetailsBean.Data.Farmerleadhistory>,
    val clickListener: OnClickListener
) : RecyclerView.Adapter<RelatedLeadAdapter.ViewHolder>() {
    var context: Context
    var closeLeadList: ArrayList<LeadDetailsBean.Data.Farmerleadhistory>

    init {
        this.context = context
        this.closeLeadList = closeLeadList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_related_lead, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return closeLeadList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.view.txtLeadId.text = "Lead ID: " + closeLeadList[position].leadCode
        holder.view.txtPurchaseAcres.text = closeLeadList[position].createdAt
        if (closeLeadList[position].leadStatus.equals("2")) {
            holder.view.txtCurrentStatus.text = "Sold"

        } else if (closeLeadList[position].leadStatus.equals("3")) {
            holder.view.txtCurrentStatus.text = "Not interested"

        }

        holder.view.llCloseLeadDetail.setOnClickListener {
            clickListener.onClick(position)

        }

//        holder.view.llCloseLeadDetail.setOnClickListener {
//            var showLeadDetail = Intent(context, LeadDetailsActivity::class.java)
//            showLeadDetail.putExtra(Constants.LESD_DETAIL_DATA, closeLeadList.get(position) as Serializable)
//            showLeadDetail.putExtra(Constants.FROM, "close")
//            context.startActivity(showLeadDetail)
//        }
//        holder.view.txtLeadId.text = "Lead ID: " + closeLeadList.get(position).lead_code
//
//        holder.view.txtLeadName.text = closeLeadList.get(position).leadName
//        holder.view.txtVillageName.text = closeLeadList.get(position).villageName
//        if (closeLeadList.get(position).purchased_area.equals("")) {
//            holder.view.txtPurchaseAcres.text = context.resources.getString(R.string.purchased_acr)
//
//        } else {
//            holder.view.txtPurchaseAcres.text = context.resources.getString(R.string.purchased_acr) + " : " + closeLeadList.get(position).purchased_area
//        }
//
//        if (closeLeadList.get(position).call_status.equals("0")) {
//
//            holder.view.imgCall.setImageResource(R.drawable.call_ylw)
//            holder.view.txtCall.setTextColor(context.resources.getColor(R.color.yello))
//        } else {
//            holder.view.imgCall.setImageResource(R.drawable.call_green)
//
//        }
//        holder.view.llCall.setOnClickListener {
//            if (closeLeadList.get(position).call_status.equals("0")) {
//                if (closeLeadList.get(position).contact_number != null && !closeLeadList.get(position).contact_number.equals("")) {
//                    var intent = Intent(Intent.ACTION_DIAL)
//                    intent.data = Uri.parse("tel:" + closeLeadList.get(position).contact_number)
//                    context.startActivity(intent)
//                }
//
//            } else {
//                Toast.makeText(context, "You have already called!", Toast.LENGTH_LONG).show()
//
//            }
//        }

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