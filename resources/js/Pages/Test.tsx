import { Head } from "@inertiajs/react";

export default function Test() {
  return (
    <>
      <Head title="Test" />
      <div style={{ padding: '2rem', fontFamily: 'sans-serif' }}>
        <h1 style={{ color: '#FF7A1F' }}>🎉 React fonctionne !</h1>
        <p>Si tu vois ce texte, Inertia + React marche bien.</p>
      </div>
    </>
  );
}
