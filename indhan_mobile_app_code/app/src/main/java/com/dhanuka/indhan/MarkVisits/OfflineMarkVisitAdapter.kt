package com.dhanuka.indhan.MarkVisits

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_mark_visit.view.*
import java.io.File


class OfflineMarkVisitAdapter(
    var context: MarkVisitOfflineActivity,
    var list: ArrayList<MarkVisitRequestModel?>
) :
    RecyclerView.Adapter<OfflineMarkVisitAdapter.ViewHolder>() {

    var layoutInflater: LayoutInflater = LayoutInflater.from(context)

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.item_mark_visit, parent, false)
        return ViewHolder(inflater)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.txtVillageName.text = list[position]?.villageName ?: ""
        holder.itemView.txtVisitDescription.text = list[position]?.farmerName ?: ""
        holder.itemView.txtRating.text = list[position]?.cropName ?: ""
        list[position]?.imgFile?.let {
            Glide.with(context)
                .load(File(it))
                .apply(RequestOptions().placeholder(R.drawable.def_user).error(R.drawable.def_user))
                .into(holder.itemView.imgVisit)
        }

    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {}
}
