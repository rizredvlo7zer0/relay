export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ success: false, msg: "Method Not Allowed" });
  }

  try {
    // Ambil body mentah (anti error parsing)
    let body = req.body;

    if (!body || typeof body !== "object") {
      body = await new Promise((resolve, reject) => {
        let data = "";
        req.on("data", chunk => (data += chunk));
        req.on("end", () => {
          try {
            resolve(JSON.parse(data || "{}"));
          } catch {
            resolve({});
          }
        });
        req.on("error", reject);
      });
    }

    // Kirim ke hosting lama
    const target = "https://viera.byethost7.com/neobank.php";

    const resp = await fetch(target, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "User-Agent": "Vercel-Relay/1.0"
      },
      body: JSON.stringify(body)
    });

    const text = await resp.text();

    return res.status(200).json({
      success: true,
      relay_status: resp.status,
      response: text
    });

  } catch (err) {
    return res.status(500).json({
      success: false,
      error: err.message
    });
  }
}