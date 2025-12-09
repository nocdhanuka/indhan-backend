package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ActivateTicket.Model.AtpList
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_atp_model.view.*

class ActivateTicketAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<AtpList>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<ActivateTicketAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<AtpList> = homeCategoryRecordResponseModelArrayList
    lateinit var onclickListener: ActivateTicketClickListener

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { onclickListener.onSelectedActivate(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: ActivateTicketClickListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_atp_model, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var msg: String = ""
        msg = plan.product ?: ""
        if (plan.dataSource.equals("Village Meeting")) {
            if (!plan.other_crop_1.isNullOrEmpty() && !plan.other_crop_2.isNullOrEmpty()) {
                msg = plan.product + " : " + plan.other_crop_1 + " : " + plan.other_crop_2
            }
        }
        holder.itemView.product_name.text = msg
        holder.itemView.crop_name.text = plan.crop
        holder.itemView.address_tv.text = plan.tehsil + ", " + plan.district + ", " + plan.state
        holder.itemView.details.text =
            "Farmers - " + plan.expectedFarmer.toString() + " PDA - " + plan.expectedPda + " Meeting - " + plan.dataSource
        val dates = Utility.getDateForView(plan.planDate)
        if (position == 0) {
            holder.itemView.date.text = dates
            holder.itemView.date.visibility = View.VISIBLE
        } else {
            if (plan.planDate.equals(filteredData[position - 1].planDate)) {
                holder.itemView.date.visibility = View.GONE
            } else {
                holder.itemView.date.visibility = View.VISIBLE
                holder.itemView.date.text = dates
            }
        }
        if (plan.planDate.contains("29")) {
            var i = 0
        }
        if (plan.status == 0 && plan.isValid < 0) {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.ivActive.text = "No Reason Provided"
            holder.itemView.ivActive.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.orange
                )
            )
        } else if (plan.status == 0 && plan.isValid >= 0) {
            holder.itemView.ivActive.visibility = View.GONE
        } else if (plan.status == 1) {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.ivActive.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.colorPrimary
                )
            )
            holder.itemView.ivActive.text = "Activated"
        } else if (plan.status == 2) {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.ivActive.text = "Reason Provided"
            holder.itemView.ivActive.setBackgroundColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.yello
                )
            )
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface ActivateTicketClickListener {
        fun onSelectedActivate(model: AtpList)
    }

}
