package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import android.annotation.SuppressLint
import android.app.Activity
import android.graphics.Color
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.PlanATP.Model.TempModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_atp_summary.view.*

//

class AtpSummaryAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<TempModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<AtpSummaryAdapter.MyViewHolder>() {

    private val filteredData: ArrayList<TempModel>
    lateinit var onclickListener: onClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onSelected() }
        }
    }

    fun setClickListener(onclickListener: onClickListener) {
        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_atp_summary, parent, false)
        return MyViewHolder(itemView)
    }

    @SuppressLint("ResourceAsColor")
    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.prod_name + " - " + plan.crop_name
        holder.itemView.date_tv.visibility = View.GONE
        showDate(holder, position)
        //holder.itemView.planned.text = "ATP Planned : " + (plan.allready_uploaded).toString()
        holder.itemView.planned.text =
            "ATP Planned : " + (plan.no_of_days + plan.allready_uploaded).toString()
        holder.itemView.plannedbys.text = "Syllabus : " + plan.syllabusDays.toString()
        if ((plan.no_of_days + plan.allready_uploaded) < plan.syllabusDays) {
            //if (( plan.allready_uploaded) < plan.syllabusDays) {
            holder.itemView.planned.setTextColor(Color.RED)
        } else {
            holder.itemView.planned.setTextColor(
                ContextCompat.getColor(
                    mActivityContext,
                    R.color.colorPrimary
                )
            )
        }
    }

    private fun showDate(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        var dates = ""
        if (plan.month != null)
            dates = Utility.getMonth(plan.month - 1).toUpperCase()
        if (position == 0) {
            holder.itemView.date_tv.text = dates
            holder.itemView.date_tv.visibility = View.VISIBLE
        } else {
            if (plan.month == filteredData[position - 1].month) {
                holder.itemView.date_tv.visibility = View.GONE
            } else {
                holder.itemView.date_tv.visibility = View.VISIBLE
                holder.itemView.date_tv.text = dates
            }
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface onClickListener {
        fun onSelected()
    }
}