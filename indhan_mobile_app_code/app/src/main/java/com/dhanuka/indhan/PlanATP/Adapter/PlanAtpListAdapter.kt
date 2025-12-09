package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.app.Activity
import android.content.Intent
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.activities.ATPDetailsActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_plan_atp_list.view.*
import java.io.Serializable

class PlanAtpListAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<PlanAtpDBModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<PlanAtpListAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<PlanAtpDBModel>

    private lateinit var onLongclickListener: BDODescriptorClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {

            view.setOnLongClickListener {
                onLongclickListener.onLongClick(
                    view,
                    filteredData.get(adapterPosition),
                    adapterPosition
                )
                true
            }

            view.imgDelete.setOnClickListener {
                onLongclickListener.onLongClick(
                    view,
                    filteredData.get(adapterPosition),
                    adapterPosition
                )
            }
        }
    }

    fun setLongClickListener(onLongclickListener: BDODescriptorClickListener) {
        this.onLongclickListener = onLongclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_plan_atp_list, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.product
        holder.itemView.crop_name.text = plan.crop
        holder.itemView.address_tv.text = plan.tehsil + ", " + plan.district + ", " + plan.state
        holder.itemView.details.text =
            "Farmers - " + plan.expected_farmer.toString() + " Acerage - " + plan.expected_pda + " Meeting - " + plan.data_source
        showDate(holder, position)

        holder.itemView.imgArrow.setOnClickListener {
            val intent = Intent(mActivityContext, ATPDetailsActivity::class.java)
            intent.putExtra(Constants.ATP_DETAIL, filteredData[position] as Serializable)
            mActivityContext.startActivity(intent)
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        var dates = ""
        val plan = filteredData[position]
        if (plan.date.contains("T"))
            dates = Utility.getDateForerrorView(plan.date)
        else
            dates = Utility.getDateForView(plan.date)

        if (position == 0) {
            holder.itemView.datetv.text = dates
            holder.itemView.datetv.visibility = View.VISIBLE

        } else {
            if (plan.date.equals(filteredData[position - 1].date)) {
                holder.itemView.datetv.visibility = View.GONE
            } else {
                holder.itemView.datetv.visibility = View.VISIBLE

                holder.itemView.datetv.text = dates
            }

        }
    }

    interface BDODescriptorClickListener {
        fun onLongClick(view: View, atpDbModel: PlanAtpDBModel, position: Int)
    }

}
